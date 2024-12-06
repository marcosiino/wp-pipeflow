<?php


require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Exceptions/PipelineExecutionException.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractPipelineStage.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/PipelineContext.php";


require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');

class SaveMediaStage extends AbstractPipelineStage
{
    private StageConfiguration $stageConfiguration;

    public function __construct(StageConfiguration $stageConfiguration)
    {
        $this->stageConfiguration = $stageConfiguration;
    }

    /**
     * @inheritDoc
     */
    public function execute(PipelineContext $context): PipelineContext
    {

        $inputMediaURLs = $this->stageConfiguration->getSettingValue("mediaURLs", $context, true);
        $convertToFormat = $this->stageConfiguration->getSettingValue("convertToFormat", $context, false, 'jpeg');
        $compression = $this->stageConfiguration->getSettingValue("compression", $context, false, '65');
        $resultTo = $this->stageConfiguration->getSettingValue("resultTo", $context, false, "SAVED_IMAGES_IDS");

        //This allows the stage to work with both a single image url or an array of image urls as input
        $mediaURLsArray = array();
        if(is_array($inputMediaURLs)) {
            $mediaURLsArray = $inputMediaURLs;
        }
        else {
            $mediaURLsArray[] = $inputMediaURLs;
        }

        $savedMediaIds = array();
        foreach($mediaURLsArray as $mediaURL) {
            $savedMediaIds[] = $this->save_image($mediaURL, $convertToFormat, $compression);
        }
        $context->setParameter($resultTo, $savedMediaIds);
        return $context;
    }
    /**
     * Download and save an image to the media library as a specified format
     *
     * @param string $image_url The URL of the image to download
     * @param string $format The desired format: 'jpeg' or 'png'
     * @param int $quality Compression quality for JPEG (default 90, ignored for PNG)
     * @return mixed The attachment ID of the saved image or throws an exception
     * @throws PipelineExecutionException
     */
    private function save_image($image_url, $format = 'jpeg', $quality = 90) {
        // Validate format
        $allowed_formats = ['jpeg', 'png'];
        if (!in_array(strtolower($format), $allowed_formats)) {
            throw new PipelineExecutionException("Unsupported format. Allowed formats are: " . implode(', ', $allowed_formats));
        }

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
        } else {
            @unlink($temp_file); // Cleanup temp file
            throw new PipelineExecutionException("Cannot detect the media file extension from the URL");
        }

        // Convert the image to the desired format
        $converted_file_path = $this->convert_image_to_format($temp_file, $format, $quality);

        // Clean up the original temporary file
        @unlink($temp_file);

        // Sets the array to feed to media_handle_sideload, with the converted image file
        $file = array(
            'name' => basename($converted_file_path), // Converted file name
            'tmp_name' => $converted_file_path, // The path to the converted file
        );

        // Upload the image in wp media
        $id = media_handle_sideload($file);

        if (is_wp_error($id)) {
            @unlink($file['tmp_name']); // Delete the converted file in case of error
            throw new PipelineExecutionException($id->get_error_message());
        }

        // Cleanup the converted file after successful upload
        @unlink($converted_file_path);

        // Return the ID of the uploaded image
        return $id;
    }

    /**
     * Convert an image file to the specified format (JPEG or PNG)
     *
     * @param string $file_path The path to the image file
     * @param string $format The desired format: 'jpeg' or 'png'
     * @param int $quality Compression quality for JPEG (ignored for PNG)
     * @return string The path to the converted file
     * @throws PipelineExecutionException
     */
    private function convert_image_to_format($file_path, $format, $quality = 90) {
        // Create an image resource from the file
        $image = imagecreatefromstring(file_get_contents($file_path));
        if ($image === false) {
            throw new PipelineExecutionException("Invalid image file");
        }

        // Determine the new file path
        $new_file_path = $file_path . '.' . $format;

        // Convert and save the image in the desired format
        switch (strtolower($format)) {
            case 'jpeg':
                if (!imagejpeg($image, $new_file_path, $quality)) {
                    imagedestroy($image);
                    throw new PipelineExecutionException("Failed to convert the image to JPEG");
                }
                break;
            case 'png':
                if (!imagepng($image, $new_file_path)) {
                    imagedestroy($image);
                    throw new PipelineExecutionException("Failed to convert the image to PNG");
                }
                break;
            default:
                imagedestroy($image);
                throw new PipelineExecutionException("Unsupported format");
        }

        // Free up memory
        imagedestroy($image);

        return $new_file_path;
    }

}