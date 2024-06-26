<?php

namespace Fromholdio\EmbedField\Forms;

use SilverStripe\Forms\FormField;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;
use SilverStripe\Forms\TextField;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Security\SecurityToken;
use SilverStripe\ORM\DataObjectInterface;
use Fromholdio\EmbedField\Model\EmbedObject;

/**
 * The form field used for creating EmbedObjects.  Basically you enter a URL and it fetches the oEmbed data from it and stores it in an EmbedObject.
 */
class EmbedField extends FormField
{
	public $embedType = false;		// video, rich, link, photo

	private static $allowed_actions = array(
		'update'
	);

	protected ?EmbedObject $object = null;
    protected ?string $sourceURL = null;

	/**
	 * Restrict what type of embed object
	 * @param string   The embed type (false (any), video, rich, link or photo)
	 */
	function setEmbedType($type = false): self
    {
		$this->embedType = $type;
        return $this;
	}

    public function getSourceURLField(): TextField
    {
        $sourceURL = $this->sourceURL ?? $this->object->SourceURL;
        $sourceURLField = TextField::create($this->getName() . '[sourceurl]', '', $sourceURL);
        $sourceURLField->setAttribute('data-update-url', $this->Link('update'));
        $sourceURLField->setAttribute('placeholder', 'https://');
        return $sourceURLField;
    }

    public function getEmbedObject(): EmbedObject
    {
        if (!$this->object || $this->object->ID == 0) {
            $this->object = EmbedObject::create();
        }
        return $this->object;
    }

    public function getThumbnail(): ?ArrayData
    {
        $data = null;
        $obj = $this->getEmbedObject();
        if ($obj->ThumbnailURL) {
            $data = ArrayData::create([
                'URL' => $obj->ThumbnailURL,
                'Title' => $obj->Title,
            ]);
        }
        return $data;
    }

	public function FieldHolder($properties = [])
    {
		Requirements::javascript('fromholdio/silverstripe-embedfield: client/js/EmbedField.js');
		Requirements::css('fromholdio/silverstripe-embedfield: client/css/EmbedField.css');
		return parent::FieldHolder($properties);
	}

	public function Type(): string
    {
		return 'embed text';
	}

	public function setValue($value, $data = null): self
    {
		if ($value instanceof EmbedObject) {
			$this->object = $value;
			parent::setValue($this->object->ID);

		}
		$this->object = EmbedObject::get()->byID($value);

		return parent::setValue($value);
	}

	public function saveInto(DataObjectInterface $record): void
    {
		$val = $this->Value();		// array[sourceurl],[data] (as json)

		$name = $this->getName();
		$sourceURL = $val['sourceurl'];

		$existingID = (int)$record->$name;

		$originalObject = EmbedObject::get()->byID($existingID);
		if (empty($sourceURL)) {
			$record->$name = 0;
			if ($originalObject) {
				$originalObject->delete();
			}
			return;
		}

		if ($originalObject?->SourceURL == $sourceURL)
        {
			// nothing has changed
			$object = $originalObject;
		}
        else {
			$existing = EmbedObject::get()->filter('SourceURL', $sourceURL)->first();
			if ($existing) {
				// save URL as an existing object
				$object = clone $existing;
				$object->ID = 0;
				$object->sourceExists = true;
			} else {
				// brand new source
				$object = EmbedObject::create();
				$object->SourceURL = $sourceURL;
				$object->updateFromURL();
			}
		}

		// delete the original object
		if ($originalObject && $originalObject->ID != $object->ID) {
			$originalObject->delete();
		}

		// write the new object
		if ($object->ID == 0) {
			$object->write();
		}

		$this->object = $object;
		$record->$name = $this->object->ID;
	}

	public function update(HTTPRequest $request)
    {
		if (!SecurityToken::inst()->checkRequest($request)) {
			return '';
		}
		$sourceURL = $request->postVar('URL');
        $this->sourceURL = $sourceURL;

		if (!empty($sourceURL))
        {
            // brand new source
            $object = EmbedObject::create();
            $object->SourceURL = $sourceURL;
            try {
                $object->updateFromURL();
            }
            catch (\Exception $e) {
                return json_encode([
                    'status' => 'invalidurl',
                    'message' => 'Please provide a correctly formatted URL.',
                    'data' => []
                ]);
            }

			if ($object->sourceExists())
            {
				if ($this->embedType && $this->embedType != $object->Type) {
                    return json_encode([
                        'status' => 'invalidurl',
                        'message' => 'Please provide a URL from a valid source type.',
                        'data' => []
                    ]);
				}

                return json_encode([
                    'status' => 'success',
                    'message' => '',
                    'data' => [
                        'ThumbnailURL' => $object->ThumbnailURL,
                        'Width' => $object->Width,
                        'Height' => $object->Height,
                        'Title' => $object->Title,
                        'Details' => $object->getDetailsForField()?->getValue()
                    ]
                ]);
			}
            else {
                return json_encode([
                    'status' => 'invalidurl',
                    'message' => 'Please provide a URL that is a valid embed source.',
                    'data' => []
                ]);
			}
		}
        else {
            return json_encode([
                'status' => 'nourl',
                'message' => '',
                'data' => []
            ]);
		}

	}

}
