<?php

declare(strict_types=1);

namespace OCA\Memories;

class FastPreview
{
    public static function intercept()
    {
        try {
            if (!\array_key_exists('fileId', $_GET) || !$_GET['fileId']) {
                return;
            }

            // Get configuration
            $config = \OC::$server->getConfig();
            $root = $config->getSystemValue('datadirectory', \OC::$SERVERROOT.'/data');
            $instanceId = $config->getSystemValue('instanceid', '');

            // Get paths
            $appFolder = 'appdata_'.$instanceId.'/preview';
            $folderPath = \OC\Preview\Storage\Root::getInternalFolder($_GET['fileId']);
            $absFolderPath = "{$root}/{$appFolder}/{$folderPath}";

            // Get preview specs
            $w = (int) $_GET['x'];
            $h = (int) $_GET['y'];
            $crop = '0' === $_GET['a'];
            $mode = 'fill';
            if (!$w || !$h) {
                return;
            }

            // Get max preview specs and extension
            [$maxWidth, $maxHeight, $ext] = self::getMaxPreview($absFolderPath);

            // Get size of the preview we want
            [$w, $h] = self::calculateSize($w, $h, $crop, $mode, $maxWidth, $maxHeight);

            // Construct filename of preview
            $filename = $w.'-'.$h;
            if ($w === $maxWidth && $h === $maxHeight) {
                $filename .= '-max';
            }
            $filename .= '.'.$ext;
            $absPath = "{$absFolderPath}/{$filename}";

            // Check file
            if (!file_exists($absPath)) {
                return;
            }

            // Send file
            if ('jpg' === $ext || 'jpeg' === $ext) {
                header('Content-Type: image/jpeg');
            } elseif ('png' === $ext) {
                header('Content-Type: image/png');
            } elseif ('gif' === $ext) {
                header('Content-Type: image/gif');
            } else {
                return; // ?
            }

            header('Content-Length: '.filesize($absPath));
            header('Content-Disposition: inline; filename="'.$filename.'"');
            header('Cache-Control: max-age=604800, private, immutable');
            header('X-Memories-FastPreview: HIT');
            header("Content-Security-Policy: default-src 'none';base-uri 'none';manifest-src 'self';frame-ancestors 'none'");
            readfile($absPath);

            // send to user
            flush();
            ob_flush();

            exit;
        } catch (\Exception $e) {
        }
    }

    /** Get file with max preview and get size and extension */
    private static function getMaxPreview(string $absFolderPath)
    {
        $files = scandir($absFolderPath);
        foreach ($files as $file) {
            if (false !== strpos($file, '-max')) {
                $parts = explode('-', $file);
                if (3 !== \count($parts)) {
                    continue;
                }
                $maxWidth = (int) $parts[0];
                $maxHeight = (int) $parts[1];

                $extParts = explode('.', $parts[2]);
                if (2 !== \count($extParts)) {
                    continue;
                }
                $ext = $extParts[1];

                return [$maxWidth, $maxHeight, $ext];
            }
        }

        throw new \Exception('No max preview found');
    }

    // Taken from @nextcloud/server Generator.php
    private static function calculateSize($width, $height, $crop, $mode, $maxWidth, $maxHeight)
    {
        /*
         * If we are not cropping we have to make sure the requested image
         * respects the aspect ratio of the original.
         */
        if (!$crop) {
            $ratio = $maxHeight / $maxWidth;

            if (-1 === $width) {
                $width = $height / $ratio;
            }
            if (-1 === $height) {
                $height = $width * $ratio;
            }

            $ratioH = $height / $maxHeight;
            $ratioW = $width / $maxWidth;

            /*
             * Fill means that the $height and $width are the max
             * Cover means min.
             */
            if ('fill' === $mode) {
                if ($ratioH > $ratioW) {
                    $height = $width * $ratio;
                } else {
                    $width = $height / $ratio;
                }
            } elseif ('cover' === $mode) {
                if ($ratioH > $ratioW) {
                    $width = $height / $ratio;
                } else {
                    $height = $width * $ratio;
                }
            }
        }

        if ($height !== $maxHeight && $width !== $maxWidth) {
            // Scale to the nearest power of four
            $pow4height = 4 ** ceil(log($height) / log(4));
            $pow4width = 4 ** ceil(log($width) / log(4));

            // Minimum size is 64
            $pow4height = max($pow4height, 64);
            $pow4width = max($pow4width, 64);

            $ratioH = $height / $pow4height;
            $ratioW = $width / $pow4width;

            if ($ratioH < $ratioW) {
                $width = $pow4width;
                $height /= $ratioW;
            } else {
                $height = $pow4height;
                $width /= $ratioH;
            }
        }

        /*
         * Make sure the requested height and width fall within the max
         * of the preview.
         */
        if ($height > $maxHeight) {
            $ratio = $height / $maxHeight;
            $height = $maxHeight;
            $width /= $ratio;
        }
        if ($width > $maxWidth) {
            $ratio = $width / $maxWidth;
            $width = $maxWidth;
            $height /= $ratio;
        }

        return [(int) round($width), (int) round($height)];
    }
}
