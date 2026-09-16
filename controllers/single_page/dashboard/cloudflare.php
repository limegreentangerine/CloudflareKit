<?php

namespace Concrete\Package\Cloudflare\Controller\SinglePage\Dashboard;

use Concrete\Core\Entity\Package;
use Concrete\Core\Error\UserMessageException;
use Cloudflare\Api\Connection as CloudflareApi;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Site\Config\Liaison;

class Cloudflare extends DashboardPageController
{
    protected Package $pkg;

    protected Liaison $config;

    protected $helpers = [
        'form',
    ];

    protected function validate(\Concrete\Core\Http\Request $request)
    {
        $vstrings = $this->app->make('helper/validation/strings');
        $vnumbers = $this->app->make('helper/validation/numbers');

        if (!$vstrings->notempty($request->request('base_url'))) {
            $this->error->add(t('Please enter a URL'), 'base_url');
        }

        if (!$vstrings->notempty($request->request('zone_id'))) {
            $this->error->add(t('Please enter Zone ID'), 'zone_id');
        }
    }

    public function on_start()
    {
        parent::on_start();

        $this->pkg = $this->app->make(PackageService::class)->getByHandle('clouflare');
        if (!is_object($this->pkg)) throw new UserMessageException(t('Cloudflare Package not found'));
        $this->set('pkg', $this->pkg);

        $this->config = $this->pkg->getFileConfig();
        $this->set('config', $this->config);
    }

    public function save()
    {
        if ($this->request->isPost()) {
            if (!$this->token->validate('submit')) {
                $this->error->add($this->token->getErrorMessage());
            }

            if (!is_object($this->pkg)) throw new UserMessageException(t('Cloudflare Package not found'));

            $this->validate($this->request);

            if (!$this->error->has()) {
                if ($this->request->request('activate') !== null) {
                    $this->config->save('cloudflare.activate', true);
                } else {
                    $this->config->save('cloudflare.activate', false);
                }

                $this->config->save('cloudflare.base_url', $this->request->request('base_url'));
                $this->config->save('cloudflare.zone_id', $this->request->request('zone_id'));
                $this->config->save('cloudflare.token', $this->request->request('token'));

                $this->flash('success', t('Cloudflare settings saved.'));
                return $this->buildRedirect('/dashboard/cloudflare');
            }
            $this->set('formContent', $this->request->request());

        } else {
            return $this->buildRedirect('/dashboard/lgt_toolkit/cloudflare');
        }
    }

    public function clear()
    {
        $rf = $this->app->make(ResponseFactoryInterface::class);

        if (!$this->token->validate('delete-cloudflare-settings')) {
            $this->error->add($this->token->getErrorMessage());
        } else {
            if (is_object($this->pkg) && $this->config) {
                $this->config->save('cloudflare.activate', false);
                $this->config->save('cloudflare.zone_id', '');
                $this->config->save('cloudflare.token', '');

                return $rf->json(true);
            }
            $this->error->add(t('Cloudflare Package not found.'));

        }

        return $rf->json($this->error->jsonSerialize());
    }

    public function getDevelopmentMode(): string
    {
        return '';

        //TODO: look at this getDevelopmentMode() function
        $api = new CloudflareApi();
        $response = $api->getDevelopmentMode();

        if ($response->getStatusCode() !== 200) {
            $error_string = $response->getUrl() . ' - (' . $response->getStatusCode() . ' ' . $response->getStatusText($response->getStatusCode()) . ')';

            $body = $response->getBodyDecoded();
            foreach ($body->errors as $error) {
                $error_string = $error_string . ' ' . $error->message;
            }

            return $error_string;
        }

        if ($r = $response->getBodyDecoded()) {
            if ($r->result == null) {
                return $response->body;
            }

            if ($r->result->id !== 'development_mode') {
                return $response->body;
            }

            if (!$r->result->editable) {
                return t('Not editable, check token permissions');
            }

            return strtoupper($r->result->value);
        }
        return t('An Unknown Error Occurred');
    }
}
