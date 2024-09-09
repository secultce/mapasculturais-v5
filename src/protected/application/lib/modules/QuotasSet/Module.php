<?php

namespace QuotasSet;

use Carbon\Carbon;
use GuzzleHttp\Client;
use MapasCulturais\App;
use MapasCulturais\Controller;
use MapasCulturais\Entities\Agent;
use MapasCulturais\Entities\AgentMeta;
use MapasCulturais\Theme;

class Module extends \MapasCulturais\Module
{
    /** @var App $app */
    private $app;

    public function _init(): void
    {
        $this->app = App::getInstance();
        $module = $this;
        $baseUri = 'http://' . env('QUOTA_SERVICE_BASE_URL', 'quota-service:9501/api/v1');
        $headers = ['Authorization' => env('QUOTA_SERVICE_TOKEN', '26243157766b1c1b4d269ad254e113e3c89eae83478efcd38f5100db69b843e903cd445feda1f5895148cfc0f34e56bf756555b64bdd6efd6acde67ac06fbe23')];

        $this->app->hook('template(panel.<<*>>.nav.panel.accountability):after', function () use ($module) {
            /** @var Theme $this */
            if ($module->canUserAccess()) {
                $this->part('quotas-set.nav.panel');
            }
        });

        $this->app->hook('GET(panel.cotas-e-politicas)', function () use ($module) {
            /** @var Controller $this */
            $this->requireAuthentication();
            if ($module->canUserAccess()) {
                $this->render('panel-quotas-set');
            } else {
                $module->app->redirect('/panel');
            }
        });

        $this->app->hook('API(agent.findByCpfOrName)', function () use ($module, $baseUri, $headers) {
            /** @var Controller $this */
            $this->requireAuthentication();
            if (!$module->canUserAccess()) {
                $this->json('', 403);
            }

            $qb = $module->app->repo(Agent::class)->createQueryBuilder('a');
            $agents = $qb->leftJoin(AgentMeta::class, 'am',  'WITH', "a = am.owner and am.key = 'cpf'")
                ->where("lower(a.name) like '%{$this->data['keyword']}%' or am.value = '{$this->data['keyword']}'")
                ->orderBy('a.updateTimestamp', 'DESC')
                ->getQuery()
                ->getResult();

            $agentsIds = [];
            $agents = array_map(function (Agent $agent) use (&$agentsIds){
                $agentsIds[] = $agent->id;
                return [
                    'id' => $agent->id,
                    'name' => $agent->name,
                    'cpf' => $agent->cpf,
                ];
            }, $agents);

            $uri = $baseUri . '/agent?filter_agent_ids=' . implode(',', $agentsIds);
            $client = new Client();
            $response = $client->request('GET', $uri, [
                'headers' => $headers,
            ]);
            $body = json_decode($response->getBody(), true);
            $assignedAgentsIds = [];
            foreach ($body as $agent) {
                $assignedAgentsIds[$agent['quotas_policy'][0]['id']] = $agent['id'];
            }
            $agents = array_map(function ($agent) use ($assignedAgentsIds) {
                if (in_array($agent['id'], $assignedAgentsIds)) {
                    $agent['assigned'] = true;
                    $agent['assigned_id'] = array_search($agent['id'], $assignedAgentsIds);
                }
                return $agent;
            }, $agents);

            $this->json($agents);
        });

        $this->app->hook('API(agent.allWithQuotas)', function () use ($module, $baseUri, $headers) {
            /** @var Controller $this */
            $this->requireAuthentication();
            if (!$module->canUserAccess()) {
                $module->app->redirect('/panel');
                return;
            }
            $uri = $baseUri . '/agent';

            $client = new Client();
            $response = $client->request('GET', $uri, [
                'headers' => $headers,
            ]);

            $body = json_decode($response->getBody(), true);
            $this->json($body);
        });

        $this->app->hook('API(agent.assignQuota)', function () use ($module, $baseUri, $headers) {
            /** @var Controller $this */
            $this->requireAuthentication();
            if (!$module->canUserAccess()) {
                $module->app->redirect('/panel');
                return;
            }

            $quotas_policy_id = $this->data['quota_id'];
            $agent_id = $this->data['agent_id'];
            $start_date = (new Carbon($this->data['start_date']))->format('Y-m-d');

            $uri = $baseUri . '/agent-quotas';
            $client = new Client();
            $response = $client->request('POST', $uri, [
                'headers' => $headers,
                'json' => [
                    'quotas_policy_id' => $quotas_policy_id,
                    'agent_id' => $agent_id,
                    'start_date' => $start_date,
                    'created_by' => $module->app->user->id,
                ],
            ]);

            $body = json_decode($response->getBody(), true);
            $this->json($body, $response->getStatusCode());
        });

        $this->app->hook('API(agent.unassignQuota)', function () use ($module, $baseUri, $headers) {
            /** @var Controller $this */
            $this->requireAuthentication();
            if (!$module->canUserAccess()) {
                $module->app->redirect('/panel');
                return;
            }

            $agent_quota_id = $this->data['agent_quota_id'];

            $uri = $baseUri . '/agent-quotas/' . $agent_quota_id;
            $client = new Client();
            $response = $client->request('DELETE', $uri, [
                'headers' => $headers,
            ]);

            $body = json_decode($response->getBody(), true);
            $this->json($body, $response->getStatusCode());
        });

        $this->app->hook('template(registration.view.evaluationForm.technical):begin', function () use ($module, $baseUri, $headers) {
            /** @var Theme $this */
            $this->controller->requireAuthentication();
            if (!$module->canUserAccess()) {
                return;
            }
            $agent = $this->controller->requestedEntity->owner;

            $uri = $baseUri . '/agent?filter_agents_ids=' . $agent->id;
            $client = new Client();
            $response = $client->request('GET', $uri, [
                'headers' => $headers,
            ]);

            $body = json_decode($response->getBody(), true);
            $thisAgent = null;
            foreach ($body as $agentResponse) {
                if ($agentResponse['id'] === $this->controller->requestedEntity->owner->id) {
                    $thisAgent = $agentResponse;
                    break;
                }
            }
            $thisAgent = $thisAgent ?? ['id' => $agent->id, 'quotas_policy' => []];

            $response = $client->request('GET', $baseUri . '/quotas', [ 'headers' => $headers ]);
            $quotas = json_decode($response->getBody(), true);

            $this->part('registration/quotas-set.widget', ['agent' => $thisAgent, 'quotas' => $quotas]);
        });
    }

    public function register(): void
    {
        // TODO: Implement register() method.
    }

    private function canUserAccess(): bool
    {
        foreach ($this->app->user->profile->sealRelations as $sealRelation) {
            if ($sealRelation->seal->id == env('SECULT_SEAL_ID')) {
                return true;
            }
        }

        return false;
    }
}
