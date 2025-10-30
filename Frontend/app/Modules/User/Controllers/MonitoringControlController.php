<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\helper;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MonitoringControlController extends Controller
{
    protected $client;
    protected $API_URL;
    protected $helper;

    public function __construct()
    {
        $this->client = new Client();
        $this->API_URL = env('MAIN_API');
        $this->helper = new helper();
    }

    /**
     * Display monitoring rules page
     */
    public function monitoringRules()
    {
        try {
            $role = Session::get('role');

            return view('User::MonitoringControl.monitoring_rules', compact('role'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading monitoring rules: ' . $e->getMessage());
        }
    }

    /**
     * Get all monitoring rules (API call)
     */
    public function getRules(Request $request)
    {
        try {
            // Get token from session (admin_session stores the token)
            $token = Session::get('admin_session')['token'] ?? Session::get('token');
            
            if (!$token) {
                return response()->json([
                    'code' => 401,
                    'message' => 'Unauthorized. Please login again.'
                ], 401);
            }

            $skip = $request->input('skip', 0);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');

            $response = $this->client->request('GET', $this->API_URL . 'admin/monitoring-rules', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'skip' => $skip,
                    'limit' => $limit,
                    'search' => $search
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Error fetching rules: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new monitoring rule
     */
    public function createRule(Request $request)
    {
        try {
            $token = Session::get('admin_session')['token'] ?? Session::get('token');

            $response = $this->client->request('POST', $this->API_URL . 'admin/monitoring-rules', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'ruleName' => $request->input('ruleName'),
                    'description' => $request->input('description'),
                    'trackApplications' => $request->input('trackApplications', 1),
                    'trackWebsites' => $request->input('trackWebsites', 1),
                    'trackKeystrokes' => $request->input('trackKeystrokes', 1),
                    'trackScreenshots' => $request->input('trackScreenshots', 1),
                    'trackMouseClicks' => $request->input('trackMouseClicks', 1),
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $responseBody = json_decode($response->getBody(), true);
            return response()->json($responseBody, $response->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Error creating rule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update monitoring rule
     */
    public function updateRule(Request $request)
    {
        try {
            $token = Session::get('admin_session')['token'] ?? Session::get('token');
            $ruleId = $request->input('id');

            $response = $this->client->request('PUT', $this->API_URL . 'admin/monitoring-rules/' . $ruleId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'ruleName' => $request->input('ruleName'),
                    'description' => $request->input('description'),
                    'trackApplications' => $request->input('trackApplications'),
                    'trackWebsites' => $request->input('trackWebsites'),
                    'trackKeystrokes' => $request->input('trackKeystrokes'),
                    'trackScreenshots' => $request->input('trackScreenshots'),
                    'trackMouseClicks' => $request->input('trackMouseClicks'),
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $responseBody = json_decode($response->getBody(), true);
            return response()->json($responseBody, $response->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Error updating rule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete monitoring rule
     */
    public function deleteRule(Request $request)
    {
        try {
            $token = Session::get('admin_session')['token'] ?? Session::get('token');
            $ruleId = $request->input('id');

            $response = $this->client->request('DELETE', $this->API_URL . 'admin/monitoring-rules/' . $ruleId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $responseBody = json_decode($response->getBody(), true);
            return response()->json($responseBody, $response->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Error deleting rule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employees assigned to a rule
     */
    public function getRuleEmployees(Request $request)
    {
        try {
            $token = Session::get('admin_session')['token'] ?? Session::get('token');
            $ruleId = $request->input('ruleId');
            $skip = $request->input('skip', 0);
            $limit = $request->input('limit', 10);

            $response = $this->client->request('GET', $this->API_URL . 'admin/monitoring-rules/' . $ruleId . '/employees', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'skip' => $skip,
                    'limit' => $limit
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Error fetching employees: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign employees to a rule
     */
    public function assignEmployees(Request $request)
    {
        try {
            $token = Session::get('admin_session')['token'] ?? Session::get('token');
            $ruleId = $request->input('ruleId');
            $employeeIds = $request->input('employeeIds', []);

            $response = $this->client->request('POST', $this->API_URL . 'admin/monitoring-rules/' . $ruleId . '/employees', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'employeeIds' => $employeeIds
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $responseBody = json_decode($response->getBody(), true);
            return response()->json($responseBody, $response->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Error assigning employees: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unassigned employees
     */
    public function getUnassignedEmployees(Request $request)
    {
        try {
            $token = Session::get('admin_session')['token'] ?? Session::get('token');
            $skip = $request->input('skip', 0);
            $limit = $request->input('limit', 100);

            $response = $this->client->request('GET', $this->API_URL . 'admin/monitoring-rules/unassigned/employees', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'skip' => $skip,
                    'limit' => $limit
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Error fetching unassigned employees: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all employees (for dropdown)
     */
    public function getAllEmployees(Request $request)
    {
        try {
            $token = Session::get('admin_session')['token'] ?? Session::get('token');

            $response = $this->client->request('GET', $this->API_URL . 'admin/employees', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'skip' => 0,
                    'limit' => 1000
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Error fetching employees: ' . $e->getMessage()
            ], 500);
        }
    }
}

