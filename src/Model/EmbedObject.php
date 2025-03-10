<?php

namespace Fromholdio\EmbedField\Model;

use Embed\Extractor;
use SilverStripe\ORM\DataObject;
use Embed\Embed;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\ValidationResult;

class EmbedObject extends DataObject
{
    protected ?DBHTMLText $previewHTML = null;

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


    public static function retrieve_data_from_url(string $sourceURL): ?array
    {
        if (empty($sourceURL)) {
            return null;
        }

        try {
            $embed = new Embed();
            $extractor = $embed->get($sourceURL);
        }
        catch (\Exception $e) {
            return null;
        }

        return static::retrieve_data_from_extractor($extractor);
    }

    public function doRefresh(): static
    {
        $sourceURL = (string) $this->SourceURL;
        $data = static::retrieve_data_from_url($sourceURL);
        return $this->doRefreshFromData($data);
    }

    public function getEmbedData(): array
    {
        return [
            'url' => $this->URL,
            'title' => $this->Title,
            'type' =>  $this->Type,
            'embed' => [
                'html' => $this->EmbedHTML,
                'width' => $this->Width,
                'height' => $this->Height,
            ],
            'thumbnail' => [
                'url' => $this->ThumbnailURL,
                'width' => $this->ThumbnailWidth,
                'height' => $this->ThumbnailHeight,
            ],
            'provider' => [
                'url' => $this->ProviderURL,
                'name' => $this->ProviderName,
            ],
            'author' => [
                'url' => $this->AuthorURL,
                'name' => $this->AuthorName,
            ],
            'origin' => $this->Origin,
            'webpage' => $this->WebPage,
            'previewHTML' => $this->getPreviewHTML()?->getValue(),
        ];
    }


    protected static function retrieve_data_from_extractor(Extractor $extractor): ?array
    {
        $url = (string) $extractor->url;
        if (empty($url)) {
            return null;
        }

        $html = (string) $extractor->code;
        if (empty($html)) {
            return [];
        }

        $type = (string) $extractor->getOEmbed()->get('type');
        if (empty($type)) {
            $type = 'rich';
        }

        $data = [
            'url' => $url,
            'title' => (string) $extractor->title,
            'type' =>  $type,
            'embed' => [
                'html' => $html,
                'width' => (string) $extractor->getOEmbed()->get('width'),
                'height' => (string) $extractor->getOEmbed()->get('height'),
            ],
            'thumbnail' => [
                'url' => (string) $extractor->image,
                'width' => (string) $extractor->getOEmbed()->get('thumbnail_width'),
                'height' => (string) $extractor->getOEmbed()->get('thumbnail_height')
            ],
            'provider' => [
                'url' => (string) $extractor->providerUrl,
                'name' => (string) $extractor->providerName,
            ],
            'author' => [
                'url' => (string) $extractor->authorUrl,
                'name' => (string) $extractor->authorName,
            ],
            'origin' => (string) $extractor->providerUrl,
            'webpage' => (string) $extractor->url,
        ];

        $previewHTML = static::get_preview_html_from_data($url, $data);
        $data['previewHTML'] = $previewHTML?->getValue();

        return $data;
    }

    protected function doRefreshFromData(?array $data = null): static
    {
        if (is_null($data)) {
            $this->SourceURL = null;
        }
        if (empty($data)) {
            $this->URL = null;
            $this->EmbedHTML = null;
            $this->Title = null;
            $this->Type = null;
            $this->Width = null;
            $this->Height = null;
            $this->ThumbnailURL = null;
            $this->ThumbnailWidth = null;
            $this->ThumbnailHeight = null;
            $this->ProviderURL = null;
            $this->ProviderName = null;
            $this->AuthorURL = null;
            $this->AuthorName = null;
            $this->Origin = null;
            $this->WebPage = null;
        }
        else {
            $this->URL = $data['url'];
            $this->Title = $data['title'];
            $this->Type = $data['type'];
            $this->EmbedHTML = $data['embed']['html'];
            $this->Width = $data['embed']['width'];
            $this->Height = $data['embed']['height'];
            $this->ThumbnailURL = $data['thumbnail']['url'];
            $this->ThumbnailWidth = $data['thumbnail']['width'];
            $this->ThumbnailHeight = $data['thumbnail']['height'];
            $this->ProviderURL = $data['provider']['url'];
            $this->ProviderName = $data['provider']['name'];
            $this->AuthorURL = $data['author']['url'];
            $this->AuthorName = $data['author']['name'];
            $this->Origin = $data['origin'];
            $this->WebPage = $data['webpage'];
        }
        return $this;
    }


    public function validate(): ValidationResult
    {
        $validator = parent::validate();
        if (!$this->isInDB() || $this->isChanged('SourceURL')) {
            $this->doRefresh();
        }
        if (!empty($this->SourceURL) && empty($this->EmbedHTML)) {
            $validator->addError(
                'A valid embed source URL is required.'
            );
        }
        return $validator;
    }


    public function getTypeLabel(): string
    {
        $type = $this->Type;
        if (empty($type)) $type = 'Unknown';
        return match ($type) {
            'video' => 'Video',
            'rich' => 'Rich embed',
            'link' => 'Link',
            'photo' => 'Image',
            default => $type
        };
    }

    public function forTemplate(): ?DBHTMLText
    {
        if ($this->Type) {
            return $this->renderWith($this->ClassName.'_'.$this->Type);
        }
        return null;
    }

    public function getPreviewHTML(): ?DBHTMLText
    {
        $html = $this->previewHTML;
        if (empty($html) && !empty($this->SourceURL)) {
            $html = static::get_preview_html_from_object($this);
            $this->previewHTML = $html;
        }
        return $html;
    }

    public static function get_preview_html_from_url(string $sourceURL): ?DBHTMLText
    {
        $obj = static::create();
        $obj->SourceURL = $sourceURL;
        $obj->doRefresh();
        return static::get_preview_html_from_object($obj);
    }

    protected static function get_preview_html_from_data(string $sourceURL, array $data): ?DBHTMLText
    {
        $obj = static::create();
        $obj->SourceURL = $sourceURL;
        $obj->doRefreshFromData($data);
        return static::get_preview_html_from_object($obj);
    }

    protected static function get_preview_html_from_object(self $embedObject): ?DBHTMLText
    {
        if (empty($embedObject->SourceURL)) {
            return null;
        }

        $templates = [];
        if (!empty($embedObject->Type)) {
            $templates[] = $embedObject->ClassName . '_' . $embedObject->Type . '_preview';
        }
        $templates[] = $embedObject->ClassName . '_preview';
        return $embedObject->renderWith($templates);
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


    /**
     * Deprecated methods. Will be removed in future major version.
     */

    public function updateFromURL($sourceURL = null): void
    {
        $this->SourceURL = $sourceURL;
        $this->doRefresh();
    }

    public function updateFromObject(?Extractor $extractor): void
    {
        $data = is_null($extractor)
            ? null
            : static::retrieve_data_from_extractor($extractor);
        $this->doRefreshFromData($data);
    }

    public function sourceExists(): bool
    {
        return $this->isInDB() && !empty($this->EmbedHTML);
    }

    public function toArray(): array
    {
        if ($this->ID === 0) {
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
}
