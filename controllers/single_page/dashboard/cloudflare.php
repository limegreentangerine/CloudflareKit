<?php

namespace Concrete\Package\CloudflareKit\Controller\SinglePage\Dashboard;

use Exception;
use Concrete\Core\Entity\Package;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Config\Repository\Liaison;
use Concrete\Core\Error\UserMessageException;
use Cloudflare\Api\Connection as CloudflareApi;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;

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

        if (!$vstrings->notempty($request->request('base_url'))) {
            $this->error->add(t('Please enter a URL'), 'base_url');
        }

        if (!$vstrings->notempty($request->request('zone_id'))) {
            $this->error->add(t('Please enter Zone ID'), 'zone_id');
        }
    }

    public function on_start()
    {
        $this->pkg = $this->app->make(PackageService::class)->getByHandle('cloudflare_kit');
        if ($this->pkg == null) {
            throw new UserMessageException(t('CloudflareKit Package not found'));
        }
        $this->set('pkg', $this->pkg);

        $this->config = $this->pkg->getFileConfig();
        $this->set('config', $this->config);

        parent::on_start();
    }

    public function save()
    {
        if ($this->request->isPost()) {
            if (!$this->token->validate('submit')) {
                $this->error->add($this->token->getErrorMessage());
            }

            if (!is_object($this->pkg)) {
                throw new UserMessageException(t('Cloudflare Package not found'));
            }

            if ($this->request->request('activate') !== null) {
                $this->validate($this->request);
            }

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
        try {
            $api = new CloudflareApi();
            $response = $api->getDevelopmentMode();
            $body = json_decode($response->getContent());

            if ($body->result == null) {
                throw new Exception(t('Empty response'));
            }

            if ($body->result->id !== 'development_mode') {
                throw new Exception(t('Development mode status unknown'));
            }


            if (!$body->result->editable) {
                throw new Exception(t('Not editable, check token permissions'));
            }

            return strtoupper($body->result->value);
        } catch (\RuntimeException $e) {
            return $e->getMessage();
        }
    }
}
