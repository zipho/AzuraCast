<?php

namespace App\Media\FFMpeg;

use App\Media\Metadata;
use App\Media\MetadataManagerInterface;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;

class FFMpegMetadataManager implements MetadataManagerInterface
{
    protected FFMpeg $ffmpeg;

    protected FFProbe $ffprobe;

    public function __construct(FFMpeg $ffmpeg, FFProbe $ffprobe)
    {
        $this->ffmpeg = $ffmpeg;
        $this->ffprobe = $ffprobe;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata(string $path): Metadata
    {
        $info = $this->ffprobe
            ->format($path)
            ->all();

        $metadata = new Metadata();

        $metadata->setDuration($info['duration']);

        $tags = $metadata->getTags();
        foreach ($info['tags'] as $tagName => $tagValue) {
            $tags->set($tagName, $tagValue);
        }

        return $metadata;
    }

    /**
     * @inheritDoc
     */
    public function writeMetadata(Metadata $metadata, string $path): bool
    {

    }
}
