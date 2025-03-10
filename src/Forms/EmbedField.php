<?php

namespace Fromholdio\EmbedField\Forms;

use Fromholdio\EmbedField\Model\EmbedObject;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Validation\ConstraintValidator;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObjectInterface;
use SilverStripe\Security\SecurityToken;
use Symfony\Component\Validator\Constraints\Url;

class EmbedField extends TextField
{
    private static $allowed_actions = [
        'preview',
    ];

    protected $schemaComponent = 'EmbedField';

    protected $schemaDataType = FormField::SCHEMA_DATA_TYPE_CUSTOM;

    protected ?EmbedObject $embedObject = null;

    protected ?array $allowedEmbedTypes = null;

    public function setEmbedType(?string $type): static
    {
        $this->allowedEmbedTypes = empty($type) ? null : [$type];
        return $this;
    }

    public function setAllowedEmbedTypes(array|string $types): static
    {
        if (empty($types)) {
            $types = null;
        }
        elseif (is_string($types)) {
            $types = [$types];
        }
        $this->allowedEmbedTypes = $types;
        return $this;
    }

    public function getAllowedEmbedTypes(): ?array
    {
        return $this->allowedEmbedTypes;
    }

    public function isAllowedEmbedType(?string $type): bool
    {
        $types = $this->getAllowedEmbedTypes();
        return !empty($type)
            && (is_null($types) || in_array($type, $types));
    }

    public function getEmbedObject(): ?EmbedObject
    {
        return $this->embedObject;
    }

    protected function setEmbedObject(?EmbedObject $embedObject): static
    {
        $this->embedObject = $embedObject;
        return $this;
    }

    public function setValue($value, $data = null): static
    {
        $embedObject = null;
        $sourceURL = null;
        if ($value instanceof EmbedObject) {
            $embedObject = $value;
        }
        elseif (is_numeric($value)) {
            $embedObject = EmbedObject::get()->byID($value);
        }
        elseif (is_string($value) && !empty($value)) {
            $sourceURL = $value;
        }
        if (!is_null($embedObject)) {
            $sourceURL = $embedObject?->SourceURL;
        }
        $this->setEmbedObject($embedObject);
        return parent::setValue($sourceURL, $data);
    }

    public function validate($validator)
    {
        $result = true;
        if ($this->value && !ConstraintValidator::validate($this->value, new Url())->isValid()) {
            $validator->validationError(
                $this->name,
                _t(__CLASS__ . '.INVALID', 'Please enter a valid URL'),
                'validation'
            );
            $result = false;
        }
        return $this->extendValidationResult($result, $validator);
    }

    public function saveInto(DataObjectInterface $record): void
    {
        parent::saveInto($record);

        $embedObject = null;
        $value = $this->dataValue();

        $fieldName = $this->getName();
        $currID = (int) $record->$fieldName;
        $currEmbedObject = EmbedObject::get()->byID($currID);
        $currSourceURL = $currEmbedObject?->SourceURL;

        $doDeleteCurr = true;

        if (!empty($value))
        {
            if ($currSourceURL === $value) {
                $embedObject = $currEmbedObject;
                $doDeleteCurr = false;
            }
            else {
                $embedObject = EmbedObject::create();
                $embedObject->SourceURL = $value;
            }
        }

        $embedObjectID = $embedObject?->ID;
        if ($embedObjectID === 0) {
            $embedObject->write();
            $embedObjectID = $embedObject->ID;
        }

        $this->setEmbedObject($embedObject);

        if ($doDeleteCurr) {
            $currEmbedObject?->delete();
        }

        $record->setCastedField($fieldName, $embedObjectID ?? 0);
    }

    public function getAttributes(): array
    {
        return [
            ...parent::getAttributes(),
            'placeholder' => 'https://',
        ];
    }

    public function Type(): string
    {
        return 'embed text url';
    }

    public function preview(HTTPRequest $request): ?string
    {
        if (!SecurityToken::inst()->checkRequest($request)) {
            return static::build_preview_response(
                'security',
                'Security token mismatch. Please refresh the page and try again.',
            );
        }

        $data = json_decode($request->getBody(), true);
        $sourceURL = $data['source_url'] ?? null;
        if (empty($sourceURL)) {
            return static::build_preview_response('empty');
        }

        $embedData = EmbedObject::retrieve_data_from_url($sourceURL);

        if (is_null($embedData)) {
            return static::build_preview_response(
                'badformat',
                'Please provide a valid URL.',
            );
        }

        if (empty($embedData)) {
            return static::build_preview_response(
                'nomatch',
                'Please provide a URL for a valid embed source.',
            );
        }

        if (!$this->isAllowedEmbedType($embedData['type'])) {
            return static::build_preview_response(
                'badtype',
                'Please provide a URL from a valid source type.'
            );
        }

        return static::build_preview_response(
            'success',
            '',
            $embedData,
        );
    }

    protected static function build_preview_response(string $status, string $message = '', array $data = []): string
    {
        return json_encode([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ]);
    }

    protected function PreviewLink(?string $action = null): string
    {
        return '/' . ltrim($this->Link('preview'), '/');
    }

    public function getSchemaStateDefaults()
    {
        return [
            ...parent::getSchemaStateDefaults(),
            'previewURL' => $this->PreviewLink(),
            'embedData' => $this->getEmbedObject()?->getEmbedData(),
        ];
    }

    public function getSchemaDataDefaults()
    {
        $data = parent::getSchemaDataDefaults();
        $data['data']['placeholder'] = 'https://';
        return $data;
    }
}
