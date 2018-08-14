<?php
namespace org\weixin;
class WeixinResponse
{

    private $_isWxMsgCrypt = false;
    /**
     * 
     * @var WXBizMsgCrypt
     */
    private $_wxBizMsgCrypt = null;

    private $data;

    public static function response($_wxBizMsgCrypt, $requestData, $content, $type = 'text', $_isWxMsgCrypt = false)
    {
        $response = new WeixinResponse();
        $response->data = $requestData;
        $response->_isWxMsgCrypt = $_isWxMsgCrypt;
        $response->_wxBizMsgCrypt = $_wxBizMsgCrypt;
        return $response->_response($content,$type);
    }

    private function _response($content, $type)
    {
        $data = array(
            'ToUserName' => $this->data['FromUserName'],
            'FromUserName' => $this->data['ToUserName'],
            'CreateTime' => time(),
            'MsgType' => $type
        );
        if (isset($this->data['AgentID'])) {
            $data['AgentID'] = $this->data['AgentID'];
        }
        $this->data=$data;
        if ($type == "text") {
            $this->text($content);
        } elseif ($type == "music") {
            $this->music($content);
        } elseif ($type == "news") {
            $this->news($content);
        } elseif ($type == "voice") {
            $this->voice($content);
        } elseif ($type == "video") {
            $this->video($content);
        } elseif ($type == "image") {
            $this->image($content);
        } elseif ($type == "transfer_customer_service") {
            $this->transfer_customer_service($content);
        }
        $xml=XML::array2xml($this->data);
        
        $xmlString = $xml->asXML();
        if ($this->_isWxMsgCrypt == true) {
            $encryptMsg = '';
            try {
                $errCode = $this->_wxBizMsgCrypt->encryptMsg($xmlString, time(), time() - mt_rand(1000, 100000), $encryptMsg);
            } catch (\Exception $e) {}
            $xmlString = $encryptMsg;
        }
        return $xmlString;
    }

    private function text($content)
    {
        if (! $content) {
            $content = '';
        }
        $this->data['Content'] = $content;
    }

    private function transfer_customer_service($content)
    {}

    private function music($music)
    {
        list ($music['Title'], $music['Description'], $music['MusicUrl'], $music['HQMusicUrl']) = $music;
        $this->data['Music'] = $music;
    }

    private function news($news)
    {
        $articles = array();
        foreach ($news as $key => $value) {
            list ($articles[$key]['Title'], $articles[$key]['Description'], $articles[$key]['PicUrl'], $articles[$key]['Url']) = $value;
            if ($key == 0) {
                if (strpos($articles[$key]['PicUrl'], 'http://ooyyee-crm-static.qiniudn.com') === 0) {
                    if (strpos($articles[$key]['PicUrl'], 'http://ooyyee-crm-static.qiniudn.com') === 0) {
                        $articles[$key]['PicUrl'] = $articles[$key]['PicUrl'] . '?imageView/1/w/360/h/200';
                    }
                }
            } else {
                if (strpos($articles[$key]['PicUrl'], 'http://ooyyee-crm-static.qiniudn.com') === 0) {
                    $articles[$key]['PicUrl'] = $articles[$key]['PicUrl'] . '?imageView/1/w/200/h/200';
                }
            }
            if ($key >= 9) {
                break;
            }
        }
        $this->data['ArticleCount'] = count($articles);
        $this->data['Articles'] = $articles;
    }

    private function voice($voice)
    {
        list ($voice['MediaId']) = $voice;
        $this->data['Voice'] = $voice;
    }

    private function video($video)
    {
        list ($video['MediaId'], $video['Title'], $video['Description']) = $video;
        $this->data['Video'] = $video;
    }

    private function image($image)
    {
        list ($image['MediaId']) = $image;
        $this->data['Image'] = $image;
    }
}

?>