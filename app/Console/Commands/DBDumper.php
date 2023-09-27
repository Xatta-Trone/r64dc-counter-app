<?php

namespace App\Console\Commands;

use Exception;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Http\File;
use App\Models\DriveBackup;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use App\Mail\BackupNotCompleteMail;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\DbDumper\Databases\MySql;
use Illuminate\Support\Facades\Storage;

class DBDumper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:db-dumper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dumps the db and uploads to google drive';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fileName = "backup-" . Carbon::now()->format('Y-m-d') . ".sql";
        $filePath = storage_path('app/' . $fileName);
        $dumped = $this->dumpFile($filePath);

        if (!$dumped) {
            // send notification
            $this->sendMail($fileName);
            return;
        }

        // upload to google drive
        $uploaded = $this->uploadToGoogleDrive($fileName, $filePath);
        $this->deleteLocalFile($filePath);

        if ($uploaded['success'] == false) {
            // send notification
            $this->sendMail($fileName, $uploaded['error']);
            return;
        }

        // save to the db
        $this->saveToDB($fileName, $uploaded['id']);

        // delete old files
        $this->deleteOldFiles();
    }

    private function sendMail($fileName, $error = "")
    {
        Mail::to('monzurul.ce.buet@gmail.com')->send(new BackupNotCompleteMail($fileName, $error));
    }

    private function deleteLocalFile($filePath)
    {
        unlink($filePath);
    }


    private function deleteOldFiles()
    {
        $driveClient = $this->getDriveClient();
        $numberOfFilesToKeep = env('BACKUP_KEEP_NUMBER', 2);

        $backups = DriveBackup::query()->orderByDesc('id')->skip($numberOfFilesToKeep)->take(PHP_INT_MAX)->get();

        foreach ($backups as $backup) {
            try {
                $driveClient->files->delete($backup->file_id);
                $backup->delete();
            } catch (Exception $e) {
                Log::error($e);
            }
        }
    }

    private function saveToDB($fileName, $fileId): DriveBackup
    {
        return DriveBackup::create([
            'name' => $fileName,
            'file_id' => $fileId,
        ]);
    }

    private function getDriveClient(): Drive
    {
        $client = new Client();
        $client->setAuthConfig(config_path('client_secret.json'));
        $client->useApplicationDefaultCredentials();
        $client->addScope(Drive::DRIVE);
        return new Drive($client);
    }

    private function uploadToGoogleDrive($fileName, $filePath)
    {
        try {
            $driveClient = $this->getDriveClient();

            $fileMetadata = new DriveFile([
                'name' => $fileName,
                'parents' => [env('GOOGLE_DRIVE_FOLDER_ID')],
            ]);

            $content = file_get_contents($filePath);

            $file = $driveClient->files->create($fileMetadata, array(
                'data' => $content,
                'mimeType' => 'application/octet-stream',
                'uploadType' => 'multipart',
                'fields' => 'id'
            ));
            return ['success' => true, 'id' => $file->id];
        } catch (Exception $e) {
            Log::error($e);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function dumpFile(string $filePath): bool
    {
        try {
            MySql::create()
                ->setDbName(env('DB_DATABASE'))
                ->setUserName(env('DB_USERNAME'))
                ->setPassword(env('DB_PASSWORD'))
                ->dumpToFile($filePath);
            return true;
        } catch (Exception $e) {
            Log::error($e);
            return false;
        }
    }
}
