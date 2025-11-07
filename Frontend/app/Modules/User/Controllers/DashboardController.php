<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\helper;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
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
     * Display the dashboard page
     */
    public function index(Request $request)
    {
        try {
            $role = Session::get('role');
            
            if (!Session::has('admin_session')) {
                return redirect('admin-login');
            }

            return view('User::Dashboard.dashboard', compact('role'));
        } catch (\Exception $e) {
            Log::error('Exception in Dashboard index: ' . $e->getMessage());
            return redirect('/admin-login')->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Get all dashboard stats
     */
    public function getDashboardStats(Request $request)
    {
        try {
            $period = $request->input('period', 'today');
            $limit = $request->input('limit', 10);
            
            $token = Session::get('admin_session')['token'];
            
            $response = $this->client->request('GET', $this->API_URL . 'dashboard/all-stats', [
                'query' => [
                    'period' => $period,
                    'limit' => $limit
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Exception in getDashboardStats: ' . $e->getMessage());
            return response()->json([
                'code' => 500,
                'message' => 'Error fetching dashboard stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employee statistics
     */
    public function getEmployeeStats(Request $request)
    {
        try {
            $date = $request->input('date');
            $token = Session::get('admin_session')['token'];
            
            $query = [];
            if ($date) {
                $query['date'] = $date;
            }
            
            $response = $this->client->request('GET', $this->API_URL . 'dashboard/employee-stats', [
                'query' => $query,
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Exception in getEmployeeStats: ' . $e->getMessage());
            return response()->json([
                'code' => 500,
                'message' => 'Error fetching employee stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get top applications
     */
    public function getTopApplications(Request $request)
    {
        try {
            $period = $request->input('period', 'today');
            $limit = $request->input('limit', 10);
            $token = Session::get('admin_session')['token'];
            
            $response = $this->client->request('GET', $this->API_URL . 'dashboard/top-applications', [
                'query' => [
                    'period' => $period,
                    'limit' => $limit
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Exception in getTopApplications: ' . $e->getMessage());
            return response()->json([
                'code' => 500,
                'message' => 'Error fetching top applications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get top websites
     */
    public function getTopWebsites(Request $request)
    {
        try {
            $period = $request->input('period', 'today');
            $limit = $request->input('limit', 10);
            $token = Session::get('admin_session')['token'];
            
            $response = $this->client->request('GET', $this->API_URL . 'dashboard/top-websites', [
                'query' => [
                    'period' => $period,
                    'limit' => $limit
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Exception in getTopWebsites: ' . $e->getMessage());
            return response()->json([
                'code' => 500,
                'message' => 'Error fetching top websites',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employees by active hours
     */
    public function getEmployeesByActiveHours(Request $request)
    {
        try {
            $period = $request->input('period', 'today');
            $limit = $request->input('limit', 5);
            $token = Session::get('admin_session')['token'];
            
            $response = $this->client->request('GET', $this->API_URL . 'dashboard/employees-active-hours', [
                'query' => [
                    'period' => $period,
                    'limit' => $limit
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Exception in getEmployeesByActiveHours: ' . $e->getMessage());
            return response()->json([
                'code' => 500,
                'message' => 'Error fetching employees by active hours',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employees by productive hours
     */
    public function getEmployeesByProductiveHours(Request $request)
    {
        try {
            $period = $request->input('period', 'today');
            $limit = $request->input('limit', 5);
            $token = Session::get('admin_session')['token'];
            
            $response = $this->client->request('GET', $this->API_URL . 'dashboard/employees-productive-hours', [
                'query' => [
                    'period' => $period,
                    'limit' => $limit
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Exception in getEmployeesByProductiveHours: ' . $e->getMessage());
            return response()->json([
                'code' => 500,
                'message' => 'Error fetching employees by productive hours',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

