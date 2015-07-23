<?php

class ImageController extends ControllerBase
{
    public function uploadAction()
    {
        $uid = $this->session->get('user-id');

        if($uid && $this->request->isPost() && $this->request->hasFiles())
        {
            $files = array();
            $names = $this->request->getPost('filename');

            $images = array();

            $user = User::find($uid)->getFirst();

            if($user)
            {
                $this->view->disable();
                $this->response->setContentType('application/json', 'UTF-8');

                $diskSpace = $user->getDiskSpace(); // Mb
                $usedSpace = $user->getUsedSpace(); // Kb
                $freeSpace = $diskSpace*1024*1024 - $usedSpace; // byte

                $uploadSize = 0;
                foreach ($this->request->getUploadedFiles() as $file) {
                    $uploadSize += $file->getSize();
                }

                if($uploadSize > $freeSpace)
                {
                    $this->response->setContent(json_encode(array(
                        'status' => false,
                        'message' => $this->getTranslation()->_('out-of-space'),
                        'uploadSize' => $uploadSize
                    )));
                }
                else
                {
                    foreach ($this->request->getUploadedFiles() as $index => $file) {

                        $image = new Image();
                        $image->UserID = $uid;

                        $image->FileName = $names[$index];
                        $image->OriginName = $file->getName();
                        $image->FileSize = $file->getSize();

                        $time = array_sum( explode( ' ' , microtime() ) );

                        $image->OriginPath = 'files/'.$time.'_'.$image->OriginName;
                        $image->ThumbPath = 'files/thumb/300_150_'.$time.'_'.$image->OriginName;

                        $file->moveTo($image->OriginPath);

                        $thumb = new \Phalcon\Image\Adapter\GD($image->OriginPath);
                        $thumb->resize(300, 150);
                        $thumb->save($image->ThumbPath);

                        $images[] = $image;

                        $image->save();
                    }

                    $this->response->setContent(json_encode(array(
                        'status' => true,
                        'message' => $this->getTranslation()->_('upload-success'),
                        'uploadSize' => $uploadSize,
                        'images' => $images
                    )));
                }
                $this->response->send();
            }
        }
    }

    public function deleteAction()
    {
        $uid = $this->session->get('user-id');
        if($uid && $this->request->isPost())
        {
            $this->view->disable();
            $this->response->setContentType('application/json', 'UTF-8');

            $imageID = $this->request->getPost('pid');
            if($imageID)
            {
                $image = Image::find($imageID)->getFirst();
                unlink($image->OriginPath);
                unlink($image->ThumbPath);
                $image->delete();
                $this->response->setContent(json_encode(array(
                    'status' => true
                )));
            }
            else $this->response->setContent(json_encode(array(
                'status' => false,
                'pid' => $imageID
            )));
            $this->response->send();
        }
    }

    public function editAction()
    {
        $uid = $this->session->get('user-id');
        if($uid && $this->request->isPost())
        {
            $this->view->disable();
            $this->response->setContentType('application/json', 'UTF-8');

            if($this->request->hasPost('pid'))
            {
                $image = Image::find($this->request->getPost('pid'));
                $image->FileName = $this->request->getPost('filename');

                if($this->request->hasFiles())
                {
                    $file = $this->request->getUploadedFile();
                    print_r($file);
                    echo "string f";
                }
                else echo $this->request->hasFiles();
            }
        }
    }
}