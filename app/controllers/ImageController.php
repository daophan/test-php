<?php

class ImageController extends ControllerBase
{
    public function uploadAction()
    {
        $uid = $this->session->get('user-id');

        if($uid && $this->request->isPost() && $this->request->hasFiles())
        {
            $e = new Phalcon\Escaper();

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

                        $fileNameArray = explode('.', $file->getName());
                        if(sizeof($fileNameArray) > 1)
                            $ext = end($fileNameArray);
                        else $ext = 'jpg';

                        if(!in_array($file->getRealType(), array('image/gif', 'image/jpeg' , 'image/png', 'image/bmp')))
                           break;

                        $image = new Image();
                        $image->UserID = $uid;

                        $image->FileName =  $e->escapeHtml($names[$index]);
                        if (strlen($image->FileName) > 20) {
                            $image->FileName = substr($image->FileName, 0, 20);
                        }
                        $image->OriginName = $file->getName();
                        $image->FileSize = $file->getSize();

                        $time = array_sum( explode( ' ' , microtime() ) );

                        $image->OriginPath = 'files/'.$time.'.'.$ext;
                        $image->ThumbPath = 'files/thumb/300_150_'.$time.'.'.$ext;

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
                if($image->UserID != $uid)
                {
                     $this->response->setContent(json_encode(array(
                        'status' => false,
                        'message' => $this->getTranslation()->_('no-permission')
                    )));
                } else {
                    unlink($image->OriginPath);
                    unlink($image->ThumbPath);
                    $image->delete();
                    $this->response->setContent(json_encode(array(
                        'status' => true
                    )));
                }
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
            $e = new Phalcon\Escaper();

            $this->view->disable();
            $this->response->setContentType('application/json', 'UTF-8');

            $data = array();

            if($this->request->hasPost('pid'))
            {
                try {
                    $image = Image::find($this->request->getPost('pid'))->getFirst();

                    if($image->UserID != $uid)
                    {
                         $this->response->setContent(json_encode(array(
                            'status' => false,
                            'message' => $this->getTranslation()->_('no-permission')
                        )));
                    }
                    else
                    {
                        $data['FileName'] = $e->escapeHtml($this->request->getPost('filename'));
                        if (strlen($data['FileName']) > 20) {
                            $data['FileName'] = substr($image->FileName, 0, 20);
                        }

                        if($this->request->hasFiles())
                        {
                            $time = array_sum( explode( ' ' , microtime() ) );

                            $file = $this->request->getUploadedFiles('file');
                            $data['OriginName'] = $file[0]->getName();
                            $data['FileSize'] = $file[0]->getSize();

                            $data['OriginPath'] = 'files/'.$time.'_'.hash($data['OriginName']);
                            $data['ThumbPath'] = 'files/thumb/300_150_'.$time.'_'.hash($data['OriginName']);

                            $file[0]->moveTo($data['OriginPath']);

                            $thumb = new \Phalcon\Image\Adapter\GD($data['OriginPath']);
                            $thumb->resize(300, 150);
                            $thumb->save($data['ThumbPath']);

                            unlink($image->OriginPath);
                            unlink($image->ThumbPath);
                        }

                        $image->update($data);
                        $this->response->setContent(json_encode(array(
                            'status' => true
                        )));
                    }
                } catch (Exception $e) {
                    $this->response->setContent(json_encode(array(
                        'status' => false,
                        'message' => $this->getTranslation()->_('edit-error')
                    )));
                }
            }
        }

        $this->response->send();
    }
}