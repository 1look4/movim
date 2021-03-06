<?php

use Movim\Widget\Base;

use Moxl\Xec\Action\Pubsub\GetConfig;
use Moxl\Xec\Action\Pubsub\SetConfig;
use Moxl\Xec\Action\Avatar\Get as AvatarGet;
use Moxl\Xec\Action\Avatar\Set as AvatarSet;

use Respect\Validation\Validator;

use Movim\Picture;

class CommunityConfig extends Base
{
    public function load()
    {
        $this->registerEvent('pubsub_getconfig_handle', 'onConfig');
        $this->registerEvent('pubsub_setconfig_handle', 'onConfigSaved');
        $this->registerEvent('avatar_set_pubsub', 'onAvatarSet');
    }

    public function onConfig($packet)
    {
        list($config, $origin, $node, $advanced) = array_values($packet->content);

        $view = $this->tpl();

        $xml = new \XMPPtoForm;
        $form = $xml->getHTML($config->x);

        $view->assign('form', $form);
        $view->assign('server', $origin);
        $view->assign('node', $node);
        $view->assign('config', ($advanced) ? false : $xml->getArray($config->x));
        $view->assign('attributes', $config->attributes());

        Dialog::fill($view->draw('_communityconfig'), true);
    }

    public function onAvatarSet($packet)
    {
        $this->rpc('Dialog_ajaxClear');
        Notification::append(null, $this->__('avatar.updated'));
    }

    public function onConfigSaved()
    {
        Notification::append(false, $this->__('communityaffiliation.config_saved'));
    }

    public function ajaxGetAvatar($origin, $node)
    {
        if (!$this->validateServerNode($origin, $node)) {
            return;
        }

        $view = $this->tpl();
        $view->assign('info', \App\Info::where('server', $origin)
                                       ->where('node', $node)
                                       ->first());

        Dialog::fill($view->draw('_communityconfig_avatar'));
    }

    public function ajaxSetAvatar($origin, $node, $form)
    {
        if (!$this->validateServerNode($origin, $node)) {
            return;
        }

        $key = $origin.$node.'avatar';

        $p = new Picture;
        $p->fromBase($form->photobin->value);
        $p->set($key, 'jpeg', 60);

        $r = new AvatarSet;
        $r->setTo($origin)
          ->setNode($node)
          ->setUrl($p->getOriginal($key))
          ->setData($p->toBase())->request();
    }

    public function ajaxGetConfig($origin, $node, $advanced = false)
    {
        if (!$this->validateServerNode($origin, $node)) {
            return;
        }

        $r = new GetConfig;
        $r->setTo($origin)
          ->setNode($node);

        if ($advanced) {
            $r->enableAdvanced();
        }

        $r->request();
    }

    public function ajaxSetConfig($data, $origin, $node)
    {
        if (!$this->validateServerNode($origin, $node)) {
            return;
        }

        $r = new SetConfig;
        $r->setTo($origin)
          ->setNode($node)
          ->setData($data)
          ->request();
    }

    private function validateServerNode($origin, $node)
    {
        $validate_server = Validator::stringType()->noWhitespace()->length(6, 40);
        $validate_node = Validator::stringType()->length(3, 100);

        return ($validate_server->validate($origin)
             && $validate_node->validate($node));
    }
}
