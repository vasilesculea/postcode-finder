<?php

namespace App\Console\Commands;

use Exception;
use ZipArchive;
use App\Postcode;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use App\Values\SpatialTypes\Point;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

/**
 * There is space for more debug information for the given command.
 * It usually will run from cron job and need some more things to be done.
 *
 * For Example: Generating filename by current date and check if it exists on server,
 * if not try to generate for previous month and so forth untill there is a file...
 *
 */
class ImportOnsPostcodeDirectory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:ons-postcode-directory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download and Import ONS Postcode Directory';

    /**
     * Temporary working directory for importing postcodes.
     *
     * @var string
     */
    protected $tmpPath;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Temporary working directory, at the end remove it.
        if ( ! file_exists($this->tmpPath = storage_path('import-ons-postcode/' . date('Y-m-d')))) {
            File::makeDirectory($this->tmpPath, 0775, true);
        }

        // We can accept this url by Option / Argument or is better to try to generate it on fly to have always newer version...
        $downloadUrl = 'http://parlvid.mysociety.org/os/ONSPD_NOV_2017_UK.zip';

        $this->info("Downloading postcodes...");

        if ( ! $this->downloadFile($downloadUrl, $filePath = "{$this->tmpPath}/onspd.zip")) {
            return $this->cleanUp();
        }

        $this->info("Unzipping postcodes...");

        if ($this->extractFile($filePath, $this->tmpPath) !== true) {
            return $this->cleanUp();
        }

        $this->info("Importing postcodes to database...");

        if (empty($files = File::allFiles("{$this->tmpPath}/Data/multi_csv"))
            || ($totalFiles = count($files)) <= 0) {
            $this->error("multi_csv folder is empty or doesn't exist.");

            return $this->cleanUp();
        }

        $this->info("There are '{$totalFiles}' files to import.");

        $progressBar = $this->output->createProgressBar($totalFiles);

        $progressBar->advance();

        foreach ($files AS $file) {
            $this->importPostCodesFromFile((string) $file);

            $progressBar->advance();
        }

        $progressBar->finish();

        $this->info("\nPostcodes were imported successfully.");

        return $this->cleanUp();
    }

    /**
     * Import all postcodes from the given file.
     *
     * @param  string  $file
     * @return void
     */
    protected function importPostCodesFromFile($file)
    {
        $header       = [];
        $fileStream   = fopen($file, 'r');
        $rowIncrement = 0;

        while (($row = fgetcsv($fileStream)) !== false) {
            if (empty($row)) {
                continue;
            }

            if ($rowIncrement++ === 0) {
                $header = $row;

                continue;
            }

            $data = array_combine($header, $row);

            if ( ! ($postCode = Arr::get($data, 'pcd'))
                || ! ($lat = Arr::get($data, 'lat')) || $lat != floatval($lat)
                || ! ($lng = Arr::get($data, 'long')) || $lng != floatval($lng)) {
                continue;
            }

            $point = new Point($lat, $lng);

            $postCode = tap(Postcode::firstOrCreate(['postcode' => $postCode], ['point' => $point]))
                ->update([
                    'lat'   => $lat,
                    'lng'   => $lng,
                    'point' => $point
                ]);
        }
    }

    /**
     * Download ons postcode file.
     *
     * @param  string   $source
     * @param  string   $target
     * @return boolean
     */
    protected function downloadFile($source, $target)
    {
        try {
            $client = new Client();
            $client->request('GET', $source, ['sink' => $target]);
        }
        catch (Exception $e) {
            $this->error("Cannot download postcodes from: {$downloadUrl}, Please check if you provided the right location.");

            return false;
        }

        return true;
    }

    /**
     * Unzip a zip file to the target path
     *
     * @param  $file
     * @param  $target
     * @return boolean
     */
    function extractFile($file, $target)
    {
        $zip = new ZipArchive;

        if (($response = $zip->open($file)) === true)
        {
            $zip->extractTo($target);
            $zip->close();

            return true;
        }

        $this->error("ZipArchive error code: {$response}");

        return false;
    }

    /**
     * Clean up everything.
     *
     * @return boolean
     */
    protected function cleanUp()
    {
        if ($this->tmpPath) {
            File::deleteDirectory($this->tmpPath, true);
        }

        return true;
    }
}
