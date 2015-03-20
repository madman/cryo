<?php
namespace Pages\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Action extends Page
{

    /**
     * @Field
     */
    protected $type = 'action';
    /**
     * @Field
     */
    protected $short_content;

    /**
     * @Field
     */
    protected $image;

    private $file;

    public function beforeSave()
    {
        parent::beforeSave();
        $this->upload();

        if (null !== $this->getFile()) {
            $this->image = $this->getFile()->getClientOriginalName();
            $this->file  = null;
        }
    }

    protected function getAbsolutePath()
    {
        return (null === $this->image) ? : $this->getUploadRootDir() . '/' . $this->image;
    }

    protected function getWebPath()
    {
        return (null === $this->image) ? : $this->getWebUploadDir() . '/' . $this->image;
    }

    protected function getUploadRootDir()
    {
        return CORE_UPLOAD_DIR . '/actions';
    }

    protected function getWebUploadDir()
    {
        return CORE_UPLOADS_URL . '/actions';
    }

    /**
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->getFile()->getClientOriginalName()
        );
    }
}