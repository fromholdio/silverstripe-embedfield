<?php

namespace Fromholdio\EmbedField\Model;

use SilverStripe\ORM\DataObject;
use Embed\Embed;
use SilverStripe\ORM\FieldType\DBHTMLText;

/**
 * Represents an oembed object.  Basically populated from oembed so the front end has quick access to properties.
 */
class EmbedObject extends DataObject
{
    private static $table_name = 'EmbedObject';

    private static $db = [
        'SourceURL' => 'Varchar(255)',
        'Title' => 'Varchar(255)',
        'Type' => 'Varchar(255)',
        'Version' => 'Float',

        'Width' => 'Int',
        'Height' => 'Int',

        'ThumbnailURL' => 'Varchar(355)',
        'ThumbnailWidth' => 'Int',
        'ThumbnailHeight' => 'Int',

        'ProviderURL' => 'Varchar(255)',
        'ProviderName' => 'Varchar(255)',

        'AuthorURL' => 'Varchar(255)',
        'AuthorName' => 'Varchar(255)',

        'EmbedHTML' => 'HTMLText',
        'URL' => 'Varchar(355)',
        'Origin' => 'Varchar(355)',
        'WebPage' => 'Varchar(355)'
    ];

    public $updateOnSave = false;

    public $sourceExists = false;

    public function getTypeLabel(): string
    {
        switch ($this->Type) {
            case 'video':
                return 'Video';
            case 'rich':
                return 'Rich embed';
            case 'link':
                return 'Link';
            case 'photo':
                return 'Image';
            default:
                return $this->Type;
        }
    }

    public function sourceExists(): bool
    {
        return ($this->isInDB() || $this->sourceExists);
    }

    public function updateFromURL($sourceURL = null): void
    {
        if (empty($sourceURL)) {
            $sourceURL = $this->SourceURL;
        }
        $embed = new Embed();
        $info = $embed->get($sourceURL);
        $this->updateFromObject($info);
    }

    public function updateFromObject($info): void
    {
        // Previously this line checked width. Unsure if this was just to
        // check if object was populated, or if width was of specific importance
        // Assuming the former and checking URL instead
        if ($info?->url)
        {
            $embed = (string) $info->code;
            if (empty($embed)) {
                $this->sourceExists = false;
            }
            else {
                $this->EmbedHTML = $embed;
                $this->sourceExists = true;

                $this->Title = $info->title;
                $this->Type = $info->getOEmbed()->get('type') ? (string) $info->getOEmbed()->get('type') : 'rich';
                $this->Width = $info->getOEmbed()->get('width') ? (string) $info->getOEmbed()->get('width') : '';
                $this->Height = $info->getOEmbed()->get('height') ? (string) $info->getOEmbed()->get('height') : '';

                $this->ThumbnailURL = (string) $info->image;
                $this->ThumbnailWidth = $info->getOEmbed()->get('thumbnail_width') ? (string) $info->getOEmbed()->get('thumbnail_width') : '';
                $this->ThumbnailHeight = $info->getOEmbed()->get('thumbnail_height') ? (string) $info->getOEmbed()->get('thumbnail_height') : '';

                $this->ProviderURL = (string) $info->providerUrl;
                $this->ProviderName = $info->providerName;

                $this->AuthorURL = (string) $info->authorUrl;
                $this->AuthorName = $info->authorName;

                $this->URL = (string) $info->url;
                $this->Origin = (string) $info->providerUrl;
                $this->WebPage = (string) $info->url;
            }
        }
        else {
            $this->sourceExists = false;
        }
    }

    /**
     * Return the object's properties as an array
     * @return array
     */
    public function toArray(): array
    {
        if ($this->ID == 0) {
            return [];
        }
        $array = $this->toMap();
        unset($array['Created']);
        unset($array['Modified']);
        unset($array['ClassName']);
        unset($array['RecordClassName']);
        unset($array['ID']);
        unset($array['SourceURL']);
        return $array;
    }

    public function onBeforeWrite(): void
    {
        parent::onBeforeWrite();
        if ($this->updateOnSave === true) {
            $this->updateFromURL($this->SourceURL);
            $this->updateOnSave = false;
        }
    }


    public function forTemplate(): ?DBHTMLText
    {
        if ($this->Type) {
            return $this->renderWith($this->ClassName.'_'.$this->Type);
        }
        return null;
    }

    public function getDetailsForField(): ?DBHTMLText
    {
        if ($this->Type) {
            /** @var DBHTMLText $html */
            $html = $this->renderWith([
                $this->ClassName . '_' . $this->Type . '_detail',
                $this->ClassName . '_detail'
            ]);
            return $html;
        }
        return null;
    }

    /**
     * This is used for making videos responsive.  It uses the video's actual dimensions to calculate the height needed for it's aspect ratio (when using this technique: http://alistapart.com/article/creating-intrinsic-ratios-for-video)
     * @return string 	Percentage for use in CSS
     */
    public function getAspectRatioHeight(): string
    {
        $height = (int) $this->Height;
        $width = (int) $this->Width;
        if (empty($height) || empty($width)) {
            return '';
        }
        return ($this->Height / $this->Width) * 100 . '%';
    }

    public function getAspectRatioLabel(): string
    {
        $height = (int) $this->Height;
        $width = (int) $this->Width;
        if (empty($height) || empty($width)) {
            return '';
        }
        $gcdValue = $this->gcd($width, $height);
        $ratioWidth = $width / $gcdValue;
        $ratioHeight = $height / $gcdValue;

        // Normalize width to an integer
        $normalizedHeight = $ratioHeight / $ratioWidth;
        $normalizedWidth = 1;

        while($normalizedHeight < 1) {
            $normalizedHeight *= 2;
            $normalizedWidth *= 2;
        }

        if (floor($normalizedHeight) !== $normalizedHeight) {
            $normalizedHeight = number_format($normalizedHeight, 2);
        }

        return "{$normalizedWidth}:{$normalizedHeight}";
    }

    protected function gcd(int|float $a, int|float $b)
    {
        while ($b != 0) {
            $t = $a;
            $a = $b;
            $b = $t % $b;
        }
        return $a;
    }
}
