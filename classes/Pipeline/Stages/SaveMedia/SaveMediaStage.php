<?php

namespace Pipeline\Stages\SaveMedia;

require_once PLUGIN_PATH . "classes/Pipeline/Exceptions/PipelineExecutionException.php";
require_once PLUGIN_PATH . "classes/Pipeline/Interfaces/AbstractPipelineStage.php";
require_once PLUGIN_PATH . "classes/Pipeline/PipelineContext.php";


require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');

use Pipeline\Exceptions\PipelineExecutionException;
use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\PipelineContext;

class SaveMediaStage implements AbstractPipelineStage
{
    /**
     * The name of the input context parameter from which the url of the medias to download are taken
     *
     * @var string
     */
    private string $srcMediaURLs;

    /**
     * The name of the output context parameter to which the ids of the medias saved to the wp library after the download process is stored
     *
     * @var string
     */
    private string $resultTo;

    public function __construct(string $srcMediaURLs, string $resultTo)
    {
        $this->srcMediaURLs = $srcMediaURLs;
        $this->resultTo = $resultTo;
    }

    /**
     * @inheritDoc
     */
    public function execute(PipelineContext $context): PipelineContext
    {
        $mediaURLsArray = $context->getParameter($this->urlsParamName)->getAll();
        foreach($mediaURLsArray as $mediaURL) {
            $savedMediaId = $this->save_image($mediaURL);
            $context->setParameter($this->outputParamName, $savedMediaId);
        }
        return $context;
    }

    private function getParameterValue($param) {

    }

    private function isReference($param) {
        if(preg_match('/$$([a-zA-Z_]+)(\[\d+\])?$$/', $param, $matches, PREG_SET_ORDER)) {}
        foreach ($matches as $match) {
            //The item 1 is the name of the placeholder (without $$), the item 2, if present, is the index within [].
            if (isset($match[2])) { //If an index has been specified in the placeholder
                //Add it to the $placeholder array with both the name and the index and type = "array"
                $placeholders[] = [
                    'parameterName' => $match[1],
                    'placeholder' => $match[0],
                    'type' => 'array',
                    'index' => trim($match[2], '[]')
                ];
            } else { // If only the name of the placeholder is specified (without index) and type = "variable"
                //Add only the name to the placeholder index
                $placeholders[] = [
                    'parameterName' => $match[1],
                    'placeholder' => $match[0],
                    'type' => 'variable',
                ];
            }
        }
    }

    /**
     * Download and save an image to the media library
     *
     * @param $image_url
     * @return mixed
     * @throws PipelineExecutionException
     */
    private function save_image($image_url) {
        // Downloads the image in a temp file
        $temp_file = download_url($image_url);
        if (is_wp_error($temp_file)) {
            throw new PipelineExecutionException($temp_file->get_error_message());
        }
        // Extract the file extension
        $image_ext = pathinfo(parse_url($image_url, PHP_URL_PATH), PATHINFO_EXTENSION);

        // Rename the temp file with the extension
        if ($image_ext) {
            $new_file_path = $temp_file . '.' . $image_ext;
            rename($temp_file, $new_file_path);
            $temp_file = $new_file_path;
        }
        else {
            throw new PipelineExecutionException("Cannot detect the media file extension from the url");
        }

        // Sets the array to feed to media_handle_sideload, with the downloaded temp file and the file name to load in the media library
        $file = array(
            'name' => basename($temp_file), // The original name of the tmp file
            'tmp_name' => $temp_file, // The path to the temp file
        );

        // Upload the image in wp media
        $id = media_handle_sideload($file);

        if (is_wp_error($id)) {
            @unlink($file['tmp_name']); // Cancella il file temporaneo in caso di errore
            throw new PipelineExecutionException($id->get_error_message());
        }

        // returns the id of the image
        return $id;
    }
}