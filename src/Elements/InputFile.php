<?php
namespace FormManager\Elements;

use Psr\Http\Message\UploadedFileInterface;
use FormManager\DataElementInterface;

class InputFile extends Input implements DataElementInterface
{
    protected $attributes = ['type' => 'file'];
    protected $value;

    public function __construct()
    {
        $this->addValidator('FormManager\\Validators\\File::validate');
    }

    /**
     * @see FormManager\DataElementInterface
     *
     * {@inheritdoc}
     */
    public function load($value = null)
    {
        $this->val($value ?: '');

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function val($value = null)
    {
        if ($value === null) {
            return $this->value;
        }

        $error = null;

        if ($value instanceof UploadedFileInterface) {
            $error = $value->getError();
        } else if (isset($value['error'])) {
            $error = $value['error'];
        }

        if ($error === UPLOAD_ERR_NO_FILE) {
            $value = null;
        }

        $this->value = $value;

        return $this;
    }
}
