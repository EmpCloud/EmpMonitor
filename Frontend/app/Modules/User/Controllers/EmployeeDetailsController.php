<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\helper;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use function GuzzleHttp\Promise\all;

class EmployeeDetailsController extends Controller
{
    protected $helper;
    protected $DOMAIN;
    protected $condition;

    public function __construct()
    {
        $this->helper = new helper();
        $this->DOMAIN = env('API_HOST_V3') . 'api/' . env('API_VERSION_3');
        $this->condition = ((new helper())->getHostName() !== env("Employee"));
    }

    public function employeeTransfer()
    {
        $api_url = $this->DOMAIN . "/hrms/get-transfer";
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            ($response['code'] === 200) ? $result = $response['data'] : $result = [];
        } catch (\Exception $e) {
            $this->helper->logException("Company", $e->getMessage());
        }
        $title = "Transfer";
        return view("User::Hrms.employees.employee_transfer_details")->with(['title' => $title, 'transfers' => $result]);
    }

    public function deleteEmployeeTransfer(Request $request)
    {
        try {
            $api_url = $this->DOMAIN . '/hrms/delete-transfer';
            try {
                $response = $this->helper->postApiCall("delete", $api_url, $request->all());
                return $this->helper->responseHandlerWithoutStatusCode($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' ApiController => deleteEmployeeTransfer => Method-delete ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' ApiController => deleteEmployeeTransfer => Method-delete ');
        }
    }

    public function addEmployeeTransfer(Request $request)
    {
        $api_url = $this->DOMAIN . "/hrms/location";
        $response1 = $this->helper->postApiCall('get', $api_url, []);
        if (!empty($response1['data'])) {
            $location = $response1['data'];
        } else {
            $location = [];
        }
        $api_url = $this->DOMAIN . "/hrms/department";
        $response2 = $this->helper->postApiCall('get', $api_url, []);
        if (!empty($response2['data'])) {
            $department = $response2['data'];
        } else {
            $department = [];
        }
        $request["status"] = "1";
        $users = (new TimeAttendanceController())->users($request);
        $title = "Add New Employee Transfer";
        return view("User::Hrms.employees.add_employee_transfer")->with(['title' => $title, 'location' => $location, 'departments' => $department, 'users' => $users]);
    }

    public function createTransfer(Request $request)
    {
        $validator = $this->TransferValidation($request->all());
        if ($validator != []) return $validator;
        $data = array(
            "employee_id" => $request->employee_to_transfer,
            "transfer_date" => $request->transfer_date,
            "transfer_department" => $request->transfer_to_dept,
            "transfer_location" => $request->transfer_to_loc,
            "description" => $request->description,
        );
        $api_url = $this->DOMAIN . "/hrms/create-transfer";
        try {
            $response = $this->helper->postApiCall('post', $api_url, $data);
            return $this->helper->responseHandler($response);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->helper->guzzleErrorHandler($e, ' ApiController => createTransfer => Method-post ');
        }
    }

    public function TransferValidation($data)
    {
        $response = [];
        $validator = Validator::make($data, [
            'employee_to_transfer' => 'required',
            'transfer_date' => 'required',
            'transfer_to_dept' => 'required',
            'transfer_to_loc' => 'required',
            'description' => 'required',
        ], []);
        if ($validator->fails()) {
            $response['code'] = 201;
            $response['msg'] = $validator->errors()->all();
            $response['data'] = null;
        }
        return $response;
    }

    public function editTransfer(Request $request)
    {
        $api_url = $this->DOMAIN . "/hrms/location";
        $response1 = $this->helper->postApiCall('get', $api_url, []);
        if (!empty($response1['data'])) {
            $location = $response1['data'];
        } else {
            $location = [];
        }
        $api_url = $this->DOMAIN . "/hrms/department";
        $response2 = $this->helper->postApiCall('get', $api_url, []);
        if (!empty($response2['data'])) {
            $department = $response2['data'];
        } else {
            $department = [];
        }
        $transfer_id = $_GET['transfer_id'];
        $data = ["transfer_id" => $transfer_id];
        $api_url = $this->DOMAIN . "/hrms/get-transfer";
        $response = $this->helper->postApiCall('post', $api_url, $data);
        ($response['statusCode'] === 200) ? $result = $response['data']['data'] : $result = [];
        $request["status"] = "1";
        $users = (new TimeAttendanceController())->users($request);
        $title = "Edit Transfer";
        return view("User::Hrms.employees.edit_employee_transfer")->with(['title' => $title, "transfers" => $result, 'location' => $location, 'departments' => $department, 'users' => $users]);
    }

    public function updateTransfer(Request $request)
    {
        $validator = $this->UpdateTransferValidation($request->all());
        if ($validator != []) return $validator;
        $data = array(
            "id" => $request->id,
            "employee_id" => $request->employee_to_transfer,
            "transfer_date" => $request->transfer_date,
            "transfer_department" => $request->transfer_to_dept,
            "transfer_location" => $request->transfer_to_loc,
            "description" => $request->description,
        );
        $api_url = $this->DOMAIN . "/hrms/update-transfer";
        try {
            $response = $this->helper->postApiCall('post', $api_url, $data);
            return $this->helper->responseHandler($response);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->helper->guzzleErrorHandler($e, ' ApiController => updateTransfer => Method-post ');
        }
    }

    public function UpdateTransferValidation($data)
    {
        $response = [];
        $validator = Validator::make($data, [
            'id' => 'required',
            'employee_to_transfer' => 'required',
            'transfer_date' => 'required',
            'transfer_to_dept' => 'required',
            'transfer_to_loc' => 'required',
            'description' => 'required',
        ], []);
        if ($validator->fails()) {
            $response['code'] = 201;
            $response['msg'] = $validator->errors()->all();
            $response['data'] = null;
        }
        return $response;
    }

    public function addResignations()
    {
        $title = "Add New Employee Resignation";
        return view("User::Hrms.employees.add_employee_resignation")->with(['title' => $title]);
    }

    public function getResignations()
    {
        $api_url = $this->DOMAIN . "/hrms/termination";
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => getResignations => Method-get ');
        }
    }

    public function resignations(Request $request)
    {
        $title = "Resignations";
        $request["type"] = 2;
        $resignations = $this->getTermination($request);
        $request["status"] = 1;
        $users = (new TimeAttendanceController())->users($request);
        return view("User::HRMS_new.Employee.resignation")->with(['title' => $title, 'terminations' => $resignations, 'users' => $users]);
    }

    public function deleteResignation(Request $request)
    {
        try {
            $api_url = $this->DOMAIN . '/hrms/delete-resignations';
            try {
                $response = $this->helper->postApiCall("delete", $api_url, $request->all());
                return $this->helper->responseHandlerWithoutStatusCode($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' ApiController => deleteResignation => Method-delete ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' ApiController => deleteResignation => Method-delete ');
        }
    }

    public function createResignation(Request $request)
    {
        $validator = $this->ResignationValidation($request->all());
        if ($validator != []) return $validator;
        $data = array(
            "employee_id" => $request->resigningemp,
            "notice_date" => $request->notice_date,
            "resignation_date" => $request->resign_date,
            "reason" => $request->resignation_reason,
            "resignation_status" => '0',
        );
        $api_url = $this->DOMAIN . "/hrms/termination";
        try {
            $response = $this->helper->postApiCall('post', $api_url, $data);
            return $this->helper->responseHandler($response);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->helper->guzzleErrorHandler($e, ' ApiController => createResignation => Method-post ');
        }
    }

    public function ResignationValidation($data)
    {
        $response = [];
        $validator = Validator::make($data, [
            'resigningemp' => 'required',
            'notice_date' => 'required',
            'resign_date' => 'required',
            'resignation_reason' => 'required',
        ], []);
        if ($validator->fails()) {
            $response['code'] = 201;
            $response['msg'] = $validator->errors()->all();
            $response['data'] = null;
        }
        return $response;
    }

    public function editResignation(Request $request)
    {
        $resignation_id = $_GET['resignation_id'];
        $data = ["resignation_id" => $resignation_id];
        $api_url = $this->DOMAIN . "/hrms/get-resignations";
        $response = $this->helper->postApiCall('post', $api_url, $data);
        ($response['statusCode'] === 200) ? $result = $response['data']['data'] : $result = [];
        $title = "Edit Resignation";
        return view("User::Hrms.employees.edit_employee_resignations")->with(['title' => $title, "resignations" => $result]);
    }

    public function updateResignation(Request $request)
    {
        $validator = $this->UpdateResignationValidation($request->all());
        if ($validator != []) return $validator;
        $data = array(
            "id" => $request->id,
            "employee_id" => $request->resigningemp,
            "notice_date" => $request->notice_date,
            "resignation_date" => $request->resign_date,
            "reason" => $request->resignation_reason,
            "resignation_status" => '0',
        );
        $api_url = $this->DOMAIN . "/hrms/update-resignations";
        try {
            $response = $this->helper->postApiCall('post', $api_url, $data);
            return $this->helper->responseHandler($response);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->helper->guzzleErrorHandler($e, ' ApiController => updateResignation => Method-post ');
        }
    }

    public function UpdateResignationValidation($data)
    {
        $response = [];
        $validator = Validator::make($data, [
            'id' => 'required',
            'resigningemp' => 'required',
            'notice_date' => 'required',
            'resign_date' => 'required',
            'resignation_reason' => 'required',
        ], []);
        if ($validator->fails()) {
            $response['code'] = 201;
            $response['msg'] = $validator->errors()->all();
            $response['data'] = null;
        }
        return $response;
    }

    public function addCompaint(Request $request)
    {
        $request["status"] = "1";
        $users = (new TimeAttendanceController())->users($request);
        $title = "Add New Employee Complaint";
        return view("User::Hrms.employees.add_employee_complaints")->with(['title' => $title, 'users' => $users]);
    }

    public function addWarning(Request $request)
    {
        $request["status"] = "1";
        $users = (new TimeAttendanceController())->users($request);
        $title = "Add New Employee Warning";
        return view("User::Hrms.employees.add_employees_warning")->with(['title' => $title, 'users' => $users]);
    }

    public function awards(Request $request)
    {
        $title = "Awards List";
        return view("User::Hrms.employees.awards")->with(['title' => $title, 'awards' => $this->getAwards($request)]);
    }

    public function newAwards(Request $request)
    {
        $title = "Add New Award";
        $award = [];
        if (isset($request["award_id"])) {
            $award = $this->getAwards($request);
            if ($award["code"] == 200) {
                $title = "Edit Award";
            } else {
                Session::flash('error', $award["msg"]);
                return redirect()->back();
            }
        }
        $users = (new TimeAttendanceController())->users($request);
        return view("User::Hrms.employees.add_awards")->with(['title' => $title, 'users' => $users, 'award' => $award]);
    }

    public function createAward(Request $request)
    {
        $validator = $this->awardValidation($request->all());
        if ($validator != []) return $validator;
        $data = $request->all();
        $file = $request->award_photo;
        $pathToStorage = public_path('assets/hrms/assets/image');;
        if (!file_exists($pathToStorage))
            mkdir($pathToStorage, 0777, true);
        $filename = $file->getClientOriginalName();
        $filename = str_replace(' ', '', $filename);
        $path = $pathToStorage . "/" . $filename;
        file_put_contents($path, file_get_contents($file->path()));
        $data["award_photo"] = substr(explode("public", $path)[2],1);
        try {
            $api_url = $this->DOMAIN . '/hrms/award';
            try {
                $response = $this->helper->postApiCall("post", $api_url, $data);
                return $this->helper->responseHandler($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => createAward => Method-post ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => createAward => Method-post ');
        }
    }

    public function updateAward(Request $request)
    {
        $validator = $this->awardValidation($request->all());
        if ($validator != []) return $validator;
        $data = $request->all();
        if ($request->create) {
            $file = $request->award_photo;
            $pathToStorage = public_path('assets/hrms/assets/image');;
            if (!file_exists($pathToStorage))
                mkdir($pathToStorage, 0777, true);
            $filename = $file->getClientOriginalName();
            $filename = str_replace(' ', '', $filename);
            $path = $pathToStorage . "/" . $filename;
            file_put_contents($path, file_get_contents($file->path()));
            $data["award_photo"] = substr(explode("public", $path)[2],1);
        } else {
            $data["award_photo"] = $request->award_photo;
        }
        unset($data["create"]);
        try {
            $api_url = $this->DOMAIN . '/hrms/award';
            try {
                $response = $this->helper->postApiCall("put", $api_url, $data);
                return $this->helper->responseHandlerWithoutStatusCode($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => updateAward => Method-post ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => updateAward => Method-post ');
        }
    }

    public function getAwards($data)
    {
        try {
            $url = ($data["award_id"]) ? '?award_id=' . $data["award_id"] : "";
            $api_url = $this->DOMAIN . '/hrms/award' . $url;
            try {
                $response = $this->helper->postApiCall("get", $api_url, []);
                return $this->helper->responseHandlerWithoutStatusCode($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => getAwards => Method-get ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => getAwards => Method-get ');
        }
    }

    public function deleteAward(Request $request)
    {
        try {
            $api_url = $this->DOMAIN . '/hrms/award';
            try {
                $response = $this->helper->postApiCall("delete", $api_url, $request->all());
                return $this->helper->responseHandlerWithoutStatusCode($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => deleteAward => Method-get ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => deleteAward => Method-get ');
        }
    }

    //Promotion

    public function addPromotion(Request $request)
    {
        $title = "Add New Employee Promotion";
        $promotion = [];
        if (isset($request["promotion_id"])) {
            $promotion = $this->getPromotion($request);
            if ($promotion["code"] === 200) {
                $title = "Edit Employee Promotion";
            } else {
                Session::flash('error', $promotion["msg"]);
                return redirect()->back();
            }
        }
        $request["status"] = "1";
        $users = (new TimeAttendanceController())->users($request);
        return view("User::Hrms.employees.add_employees_promotion")->with(['title' => $title, 'users' => $users, 'promotion' => $promotion]);
    }

    public function promotionList(Request $request)
    {
        $title = "Employee's Promotion List";
        $promotion = $this->getPromotion($request);
        return view("User::Hrms.employees.employees_promotions")->with(['title' => $title, 'promotions' => $this->getPromotion($request)]);
    }

    public function createPromotion(Request $request)
    {
        $validator = $this->promotionValidation($request->all());
        if ($validator != []) return $validator;
        try {
            $api_url = $this->DOMAIN . '/hrms/promotion';
            try {
                $response = $this->helper->postApiCall("post", $api_url, $request->all());
                return $this->helper->responseHandler($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => createPromotion => Method-post ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => createPromotion => Method-post ');
        }
    }

    public function updatePromotion(Request $request)
    {
        $validator = $this->promotionValidation($request->all());
        if ($validator != []) return $validator;
        try {
            $api_url = $this->DOMAIN . '/hrms/promotion';
            try {
                $response = $this->helper->postApiCall("put", $api_url, $request->all());
                return $this->helper->responseHandlerWithoutStatusCode($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => updatePromotion => Method-post ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => updatePromotion => Method-post ');
        }
    }

    public function getPromotion($data)
    {
        try {
            $url = ($data["promotion_id"]) ? '?promotion_id=' . $data["promotion_id"] : "";
            $api_url = $this->DOMAIN . '/hrms/promotion' . $url;
            try {
                $response = $this->helper->postApiCall("get", $api_url, []);
                return $this->helper->responseHandlerWithoutStatusCode($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => getPromotion => Method-get ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => getPromotion => Method-get ');
        }
    }

    public function deletePromotion(Request $request)
    {
        try {
            $api_url = $this->DOMAIN . '/hrms/promotion';
            try {
                $response = $this->helper->postApiCall("delete", $api_url, $request->all());
                return $this->helper->responseHandlerWithoutStatusCode($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => deletePromotion => Method-get ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => deletePromotion => Method-get ');
        }
    }

    //Termination
    public function addTermination(Request $request)
    {
        $title = "Add New Employee Termination";
        $termination = [];
        if (isset($request["termination_id"])) {
            $termination = $this->getTermination($request);
            if ($termination["code"] == 200) {
                $title = "Edit Termination";
            } else {
                Session::flash('error', $termination["msg"]);
                return redirect()->back();
            }
        }
        $request["status"] = "1";
        $users = (new TimeAttendanceController())->users($request);
        return view("User::Hrms.employees.add_employees_terminations")->with(['title' => $title, 'users' => $users, 'termination' => $termination]);
    }

    public function createTermination(Request $request)
    {
        $validator = $this->terminationValidation($request->all());
        if ($validator != []) return $validator;
        try {
            $api_url = $this->DOMAIN . '/hrms/termination';
            try {
                $response = $this->helper->postApiCall("post", $api_url, $request->all());
                return $this->helper->responseHandler($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => createTermination => Method-post ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => createTermination => Method-post ');
        }
    }

    public function updateTermination(Request $request)
    {
        $validator = $this->terminationValidation($request->all());
        if ($validator != []) return $validator;
        try {
            $api_url = $this->DOMAIN . '/hrms/termination';
            try {
                $response = $this->helper->postApiCall("put", $api_url, $request->all());
                return $this->helper->responseHandlerWithoutStatusCode($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => updateTermination => Method-post ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => updateTermination => Method-post ');
        }
    }

    public function deleteTermination(Request $request)
    {
        try {
            $api_url = $this->DOMAIN . '/hrms/termination';
            try {
                $response = $this->helper->postApiCall("delete", $api_url, $request->all());
                return $this->helper->responseHandlerWithoutStatusCode($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => deleteTermination => Method-get ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => deleteTermination => Method-get ');
        }
    }

    public function getTermination($data)
    {
        try {
            $url = ($data["termination_id"]) ? '&termination_id=' . $data["termination_id"] : "";
            $api_url = $this->DOMAIN . '/hrms/termination?type='.$data["type"] . $url;
            try {
                $response = $this->helper->postApiCall("get", $api_url, []);
                return $this->helper->responseHandlerWithoutStatusCode($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => getTermination => Method-get ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => getTermination => Method-get ');
        }
    }

    public function terminationList(Request $request)
    {
        $title = "Terminations";
        $request["type"] = 1;
        $resignations = $this->getTermination($request);
        $request["status"] = 1;
        $users = (new TimeAttendanceController())->users($request);
        return view("User::HRMS_new.Employee.termination")->with(['title' => $title, 'terminations' => $resignations, 'users' => $users]);
    }

    //Travels
    public function addTravel(Request $request)
    {
        $title = "Add New Employee Travel";
        $travel = [];
        if (isset($request["travel_id"])) {
            $travel = $this->getTravel($request);
            if ($travel["code"] == 200) {
                $title = "Edit Travel";
            } else {
                Session::flash('error', $travel["msg"]);
                return redirect()->back();
            }
        }
        $request["status"] = "1";
        $users = (new TimeAttendanceController())->users($request);
        return view("User::Hrms.employees.add_employees_travels")->with(['title' => $title, 'users' => $users, 'travel' => $travel]);
    }

    public function createTravel(Request $request)
    {
        $validator = $this->travelValidation($request->all());
        if ($validator != []) return $validator;
        try {
            $api_url = $this->DOMAIN . '/hrms/travel';
            try {
                $response = $this->helper->postApiCall("post", $api_url, $request->all());
                return $this->helper->responseHandler($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => createTravel => Method-post ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => createTravel => Method-post ');
        }
    }

    public function updateTravel(Request $request)
    {
        $validator = $this->travelValidation($request->all());
        if ($validator != []) return $validator;
        try {
            $api_url = $this->DOMAIN . '/hrms/travel';
            try {
                $response = $this->helper->postApiCall("put", $api_url, $request->all());
                return $this->helper->responseHandlerWithoutStatusCode($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => updateTravel => Method-post ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => updateTravel => Method-post ');
        }
    }

    public function updateTravelStatus(Request $request)
    {
        try {
            $api_url = $this->DOMAIN . '/hrms/travel';
            try {
                $response = $this->helper->postApiCall("patch", $api_url, $request->all());
                return $this->helper->responseHandlerWithoutStatusCode($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => updateTravel => Method-post ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => updateTravel => Method-post ');
        }
    }

    public function deleteTravel(Request $request)
    {
        try {
            $api_url = $this->DOMAIN . '/hrms/travel';
            try {
                $response = $this->helper->postApiCall("delete", $api_url, $request->all());
                return $this->helper->responseHandlerWithoutStatusCode($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => deleteTravel => Method-get ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => deleteTravel => Method-get ');
        }
    }

    public function getTravel($data)
    {
        try {
            $url = ($data["travel_id"]) ? '?travel_id=' . $data["travel_id"] : "";
            $api_url = $this->DOMAIN . '/hrms/travel' . $url;
            try {
                $response = $this->helper->postApiCall("get", $api_url, []);
                return $this->helper->responseHandlerWithoutStatusCode($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => getTravel => Method-get ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => getTravel => Method-get ');
        }
    }

    public function travelList(Request $request)
    {
        $title = "Employee's Travel List";
        return view("User::Hrms.employees.employees_travel")->with(['title' => $title, 'travels' => $this->getTravel($request)]);
    }

    //Validation
    public function promotionValidation($data)
    {
        $response = [];
        $validator = Validator::make($data, [
            'employee_id' => 'required',
            'title' => 'required|regex:/^[a-zA-Zء-ي][a-zA-Zء-ي ]*$/',
            'description' => 'required|regex:/^[a-zA-Zء-ي][a-zA-Zء-ي ]*$/',
            'date' => 'required',
        ], []);
        if ($validator->fails()) {
            $response['code'] = 201;
            $response['msg'] = $validator->errors()->all();
            $response['data'] = null;
        }
        return $response;
    }

    public function terminationValidation($data)
    {
        $response = [];
        $validator = Validator::make($data, [
            'employee_id' => 'required',
            'reason' => 'required|regex:/^[a-zA-Zء-ي][a-zA-Zء-ي ]*$/',
            'notice' => 'required',
            'termination' => 'required',
            'description' => 'required|regex:/^[a-zA-Zء-ي][a-zA-Zء-ي ]*$/',
        ], [
            'notice.required' => 'The notice date is required.',
            'termination.required' => 'The termination date is required.'
        ]);
        if ($validator->fails()) {
            $response['code'] = 201;
            $response['msg'] = $validator->errors()->all();
            $response['data'] = null;
        }
        return $response;
    }

    public function awardValidation($data)
    {
        $response = [];
        $validator = Validator::make($data, [
            'award_type' => 'required|regex:/^[a-zA-Zء-ي][a-zA-Zء-ي ]*$/',
            'award_date' => 'required',
            'gift' => 'required|regex:/^[a-zA-Zء-ي][a-zA-Zء-ي ]*$/',
            'cash' => 'numeric',
            'award_info' => 'required',
            'award_photo' => 'required',
        ], []);
        if ($validator->fails()) {
            $response['code'] = 201;
            $response['msg'] = $validator->errors()->all();
            $response['data'] = null;
        }
        return $response;
    }

    public function travelValidation($data)
    {
        $response = [];
        $validator = Validator::make($data, [
            'employee_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'purpose' => 'required|regex:/^[a-zA-Zء-ي][a-zA-Zء-ي ]*$/',
            'place' => 'required|regex:/^[a-zA-Zء-ي][a-zA-Zء-ي ]*$/',
            'travel_mode' => 'required|regex:/^[a-zA-Zء-ي][a-zA-Zء-ي ]*$/',
            'expected_travel_budget' => 'required|numeric',
            'actual_travel_budget' => 'required|numeric',
            'arrangement_type' => 'required|regex:/^[a-zA-Zء-ي][a-zA-Zء-ي ]*$/',
            'description' => 'required|regex:/^[a-zA-Zء-ي][a-zA-Zء-ي ]*$/',
        ], []);
        if ($validator->fails()) {
            $response['code'] = 201;
            $response['msg'] = $validator->errors()->all();
            $response['data'] = null;
        }
        return $response;
    }

    public function compaintList()
    {
        $api_url = $this->DOMAIN . "/hrms/complaints";
        try {
            $response = $this->helper->postApiCall('get', $api_url,  []);
            ($response['code'] === 200) ? $result = $response['data'] : $result = [];
        } catch (\Exception $e) {
            $this->helper->logException("Company", $e->getMessage());
        }
        $title = "Employee's Complaint List";
        return view("User::Hrms.employees.employees_complaints")->with(['title' => $title, 'complaints' => $result]);
    }

    public function deleteComplaints(Request $request)
    {
        try {
            $api_url = $this->DOMAIN . "/hrms/delete-complaints";
            try {
                $response = $this->helper->postApiCall("delete", $api_url, $request->all());
                return $this->helper->responseHandlerWithoutStatusCode($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' ApiController => deleteComplaints => Method-delete ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' ApiController => deleteComplaints => Method-delete ');
        }
    }

    public function createComplaints(Request $request)
    {
        $validator = $this->ComplaintValidation($request->all());
        if ($validator != []) return $validator;
        $data = array(
            "complaint_from" => $request->complaint_from,
            "title" => $request->complaint_title,
            "complaint_date" => $request->complaint_date,
            "complaint_against" => $request->complaint_against,
            "description" => $request->description,
            "status" => '1',
            "type" => '1',
        );
        $api_url = $this->DOMAIN . "/hrms/create-complaints";
        try {
            $response = $this->helper->postApiCall('post', $api_url, $data);
            return $this->helper->responseHandler($response);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->helper->guzzleErrorHandler($e, ' ApiController => createResignation => Method-post ');
        }
    }

    public function ComplaintValidation($data)
    {
        $response = [];
        $validator = Validator::make($data, [
            'complaint_from' => 'required',
            'complaint_title' => 'required',
            'complaint_date' => 'required',
            'complaint_against' => 'required',
            'description' => 'required',
        ], []);
        if ($validator->fails()) {
            $response['code'] = 201;
            $response['msg'] = $validator->errors()->all();
            $response['data'] = null;
        }
        return $response;
    }

    public function warningList()
    {
        $api_url = $this->DOMAIN . "/hrms/warnings";
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            ($response['code'] === 200) ? $result = $response['data'] : $result = [];
        } catch
        (\Exception $e) {
            $this->helper->logException("Company", $e->getMessage());
        }
        $title = "Employee's Warning List";
        return view("User::Hrms.employees.employees_warning")->with(['title' => $title, 'warnings' => $result]);
    }

    public function deleteWarnings(Request $request)
    {
        try {
            $api_url = $this->DOMAIN . "/hrms/delete-warnings";
            try {
                $response = $this->helper->postApiCall("delete", $api_url, $request->all());
                return $this->helper->responseHandlerWithoutStatusCode($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' ApiController => deleteComplaints => Method-delete ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' ApiController => deleteComplaints => Method-delete ');
        }
    }

    public function createWarnings(Request $request)
    {
        $validator = $this->WarningValidation($request->all());
        if ($validator != []) return $validator;
        $data = array(
            "complaint_from" => $request->warning_by,
            "title" => $request->subject,
            "complaint_date" => $request->warning_date,
            "complaint_against" => $request->warning_to,
            "description" => $request->description,
            "warning_type" => $request->warning_type,
            "status" => '1',
            "type" => '2',
        );
        $api_url = $this->DOMAIN . "/hrms/create-warnings";
        try {
            $response = $this->helper->postApiCall('post', $api_url, $data);
            return $this->helper->responseHandler($response);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->helper->guzzleErrorHandler($e, ' ApiController => createWarnings => Method-post ');
        }
    }

    public function WarningValidation($data)
    {
        $response = [];
        $validator = Validator::make($data, [
            'warning_to' => 'required',
            'warning_by' => 'required',
            'warning_type' => 'required',
            'warning_date' => 'required',
            'subject' => 'required',
            'description' => 'required',
        ], []);
        if ($validator->fails()) {
            $response['code'] = 201;
            $response['msg'] = $validator->errors()->all();
            $response['data'] = null;
        }
        return $response;

    }

    public function attendance(Request $request)
    {
        $title = "Employee's Attendance";
        $locations = (new UserController())->getAllLocations();
        $departments = (new UserController())->getDepartments();
        return view("User::Hrms.employees.employees_attendance")->with(['title' => $title,'locations' => $locations, 'departments' => $departments, "TodayDate" => date('M/Y')]);
    }

    public function getAttendanceSheet(Request $request)
    {
        $time=getdate();
        $data['date'] = $request->date == 0 ? (($time['mon'] > 9) ? $time['year'].$time['mon'] : ($time['year'] . '0' . $time['mon'])) : $request->date;
        $employeeType = (isset($request["EMPLOYEE_TYPE"])) ? '&employee_type=' . $request["EMPLOYEE_TYPE"] : '&employee_type=0';
        try {
            $api_url = $this->DOMAIN . '/hrms/attendance?location_id='.$request->LocationId.'&department_id='.$request->DepartmentId.'&role_id=0&date='.$data['date'].'&name='.$request->searhcText.'&sortColumn='.$request->SORT_NAME.'&sortOrder='.$request->SORT_ORDER.'&skip='.$request->skip.'&limit='.$request->limit.'&status=1' .$employeeType;

            if(isset($request->dateRange)){
               $api_url .='&start_date='.$request->dateRange[0].'&end_date='.$request->dateRange[1];
            }
            try {
                $response = $this->helper->postApiCall("get", $api_url, []);
                return $this->helper->responseHandlerWithoutStatusCode($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => getAttendanceSheet => Method-get ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => getAttendanceSheet => Method-get ');
        }
    }

    public function bankDetail(Request $request)
    {
        $title = "Bank Details";
        $totalCount = 0;
        $locations = (new UserController())->getAllLocations();
        $departments = (new UserController())->getDepartments();
        $users = (new TimeAttendanceController())->users($request);
        return view("User::HRMS_new.Employee.bank_details")->with(['title' => $title,'users' => $users,'locations' => $locations, 'departments' => $departments]);
    }

    public function complianceDetail(Request $request)
    {
        $title = "Compliance Details";
        $totalCount = 0;
        $locations = (new UserController())->getAllLocations();
        $departments = (new UserController())->getDepartments();
        $users = (new TimeAttendanceController())->users($request);
        return view("User::HRMS_new.Employee.compliance_details")->with(['title' => $title,'users' => $users,'locations' => $locations, 'departments' => $departments]);
    }

    public function createBankDetail(Request $request)
    {
        $validator = $this->bankdetailValidation($request->all());
        if ($validator != []) return $validator;
        $api_url = $this->DOMAIN . '/hrms/create-bankdetails';
        try {
            $response = $this->helper->postApiCall("post", $api_url, $request->all());
            return $this->helper->responseHandler($response);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => createBankDetails => Method-post ');
        }
    }

    public function updateBankDetail(Request $request)
    {
        $validator = $this->bankdetailValidation($request->all());
        if ($validator != []) return $validator;
        $api_url = $this->DOMAIN . '/hrms/update-bankdetails';
        try {
            $response = $this->helper->postApiCall("put", $api_url, $request->all());
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => updateBankDetail => Method-put ');
        }
    }

    public function deleteBankDetails(Request $request)
    {
        $api_url = $this->DOMAIN . '/hrms/delete-bankdetails';
        try {
            $response = $this->helper->postApiCall("delete", $api_url, $request->all());
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => deleteBankDetails => Method-delete ');
        }
    }

    public function getBankDetail(Request $request)
    {
        $data = "";
        $data .= $request->searchText ? "&name=".$request->searchText : null;
        $data .= $request->SORT_NAME ? "&sortColumn=".$request->SORT_NAME : null;
        $data .= $request->SORT_ORDER ? "&sortOrder=".$request->SORT_ORDER : null;
        $data .= $request->skip ? "&skip=".$request->skip : "&skip=0";
        $data .= $request->limit ? "&limit=".$request->limit : "&limit=10";
        $data .= $request->EMPLOYEE_TYPE ? "&employee_type=".$request->EMPLOYEE_TYPE : "&employee_type=0";
        $api_url = $this->DOMAIN . "/hrms/get-bankdetails?location_id=0&department_id=0&role_id=0".$data."&status=1";
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => getBankDetail => Method-get ');
        }
    }

    public function getBasicDetail(Request $request)
    {
        $url = "";
        $request["location_id"] = ($request["location_id"]) ? $request["location_id"] : 0;
        $request["department_id"] = ($request["department_id"]) ? $request["department_id"] : 0;
        $request["role_id"] = ($request["role_id"]) ? $request["role_id"] : 0;
        $url .= ($request["searhcText"]) ? "&name=".$request["searhcText"] : "";
        $url .= ($request["SORT_NAME"]) ? "&sortColumn=".$request["SORT_NAME"] : "";
        $request["SORT_ORDER"] = ($request["SORT_ORDER"]) ? $request["SORT_ORDER"] : "A";

        $request["skip"] = (isset($request["skip"])) ? $request["skip"] : 0;
        $request["limit"] = (isset($request["limit"])) ? $request["limit"] : 10;
        $request["employee_type"] = (isset($request["EMPLOYEE_TYPE"])) ? $request["EMPLOYEE_TYPE"] : 0;
        $api_url = $this->DOMAIN . "/hrms/basic-info?location_id=".$request["location_id"]."&department_id=".$request["department_id"]."&role_id=".$request["role_id"].$url."&sortOrder=".$request["SORT_ORDER"]."&skip=".$request["skip"]."&limit=".$request["limit"]."&status=1&employee_type=".$request['employee_type'];
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => basicDetail => Method-get ');
        }
    }

    public function basicDetail(Request $request)
    {
        $title = "Basic Details";
        $basicDetails = $this->getBasicDetail($request);
        $locations = (new UserController())->getAllLocations();
        $departments = (new UserController())->getDepartments();
        $biometricDepartments = (new HrmsViewController())->getBioMetricDepartment($request);
        $users = (new TimeAttendanceController())->users($request);
        return view("User::HRMS_new.Employee.basic_details")->with(['title' => $title,'basicDetails' => $basicDetails, 'users' => $users,'locations' => $locations, 'departments' => $departments,'biometricDepartments' => $biometricDepartments]);
    }

    public function bankdetailValidation($data)
    {
        $response = [];
        $validator = Validator::make($data, [
            'employee_id' => 'required',
            'account_number' => 'required',
            'bank_name' => 'required',
            'ifsc_code' => 'required',
//            'bank_address' => 'required',
//            'ctc' => 'required',
//            'pan_number' => 'required',
        ], []);
        if ($validator->fails()) {
            $response['code'] = 201;
            $response['msg'] = $validator->errors()->all();
            $response['data'] = null;
        }
        return $response;
    }

    public function updateBasicInfo(Request $request)
    {
        $validator = $this->basicInfoValidation($request->all());
        if ($validator != []) return $validator;
        $api_url = $this->DOMAIN . '/hrms/basic-info';
        try {
            $response = $this->helper->postApiCall("put", $api_url, $request->all());
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => updateBasicInfo => Method-put ');
        }
    }

    public function attendanceNew(Request $request){
        $title = "Employee's Attendance";
        $UserController = new UserController();
        $locations = $UserController->getAllLocations();
        $departments = $UserController->getDepartments();
        $departments = $UserController->getDepartments();
        $leaves = (new PayrollController())->getLeaveTypes($request);
        return view("User::HRMS_new.Employee.attendance")->with(['title' => $title,'locations' => $locations, 'departments' => $departments, "TodayDate" => date('M/Y'), 'leaves' => $leaves]);
    }

    public function basicInfoValidation($data)
    {
        $response = [];
        $validator = Validator::make($data, [
            'id' => 'required',
            'marital_status' => 'required',
            'type'=>'required',
            'phone' => [
                'required',
                'regex:/^\d{2}-\d{10}$/',
            ],
            'email' => 'required',
            'c_address' => 'required',
            'date_of_birth' => 'required',
            'personal_email' => 'required|email',
        ], ['phone.regex'=>'phone should be in this format 91-10digits(Note:starting 2 digits are country code).',]);
        if ($validator->fails()) {
            $response['code'] = 201;
            $response['msg'] = $validator->errors()->all();
            $response['data'] = null;
        }
        return $response;
    }

    public function leaveView(Request $request)
    {
        $title = __('hrms.leaves');
        $request["leave_id"] = (isset($_GET["id"])) ? $_GET["id"] : "";
        $leaves = (new PayrollController())->getLeaves($request);
        $employeeID = null;
        $balanceLeaves = (new PayrollController())->getLeaveBalance($employeeID);
        $leave_name = array_column($balanceLeaves['data'] == null ? [] : $balanceLeaves['data'], 'leaves');

        $type = $this->helper->getHostName();

        $request["leave_id"] = "";
        return view("User::HRMS_new.Employee.leaves")->with([
            'title' => $title,
            'leaveName' => $leave_name,
            'users' => (new TimeAttendanceController())->users($request),
            'leaves' => $leaves,
            'leaveTypes' => (new PayrollController())->getLeaveTypes($request),
            'condition' => $this->condition,
            'UserName' => Session::get('employee')['token']['full_name'] ?? Session::get('admin')['token']['login'],
            'type' => $type
        ]);
    }

    public function leaveDetailsView(Request $request)
    {
        $title = "Leave Details";
        $request["leave_id"] = (isset($_GET["id"])) ? $_GET["id"] : "";
        return view("User::HRMS_new.Attendance.leave_details")->with([
            'title' => $title,
            'leaves' => (new PayrollController())->leavesDetails($request),
            'condition' => $this->condition
        ]);
    }


    public function approveRejectLeave(Request $request)
    {
        try {
            $api_url = $this->DOMAIN . '/hrms/leave-details';
            $response = $this->helper->postApiCall("put", $api_url, $request->all());
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => updateHoliday => Method-put ');
        }
    }

    public function dahsboardView()
    {
        $title = __('hrms.dashboard');
        $description = "";
        if ($this->helper->checkEnvPermission('CUSTOM_FEATURE_HRMS_EMPMONITOR')){

            $attendanceStatus = $this->getAttendanceStatus();
            $dailyScans = $this->getDailyScans();
            $weeklyMonthlyScans = $this->getWeeklyMonthlyScans();
            $dailyEmployeeDetails = $this->getDailyEmployeeDetails();
            $newRegisterGraphs = $this->getNewRegisterGraphs();
            return view("User::HRMS_new.Home.dashboard_custom")->with([ 'title' => $title, 'description' => $description, 'attendanceStatus' => $attendanceStatus , 'weeklyMonthlyScans' => $weeklyMonthlyScans,'dailyScans' => $dailyScans ,'dailyEmployeeDetails' => $dailyEmployeeDetails, 'newRegisterGraphs' => $newRegisterGraphs]);
        }else{
            return view("User::HRMS_new.Home.dashboard")->with([ 'title' => $title, 'description' => $description ]);
        }
    }
    public function getAttendanceStatus()
    {
        $api_url = $this->DOMAIN . "/hrms/attendance-status";
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $response;
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => AllDetails => Method-get ');
        }
    }
    public function getDailyScans()
    {
        $api_url = $this->DOMAIN . "/hrms/daily-scans";
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $response;
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => AllDetails => Method-get ');
        }
    }
    public function getWeeklyMonthlyScans()
    {
        $api_url = $this->DOMAIN . "/hrms/weekly-monthly-scans";
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $response;
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => AllDetails => Method-get ');
        }
    }
    public function getDailyEmployeeDetails()
    {
        $api_url = $this->DOMAIN . "/hrms/daily-employee-details";
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $response;
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => AllDetails => Method-get ');
        }
    }
    public function getNewRegisterGraphs()
    {
        $api_url = $this->DOMAIN . "/hrms/new-register-graphs";
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $response;
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => AllDetails => Method-get ');
        }
    }
    public function biometricSettingsView(Request $request)
    {
        $title = __('hrms.biometric_setting');
        $result = $this->biometricStatus();
        $empList = $this->biometricEmpListStatus();
        $confirmId = $this->biometricGetConfirmIdStatus();
        $cameraId = $this->biometricGetbiometricCameraStatus();
        return view("User::HRMS_new.Attendance.biometric")->with(['title' => $title, 'bioStatus' => $result, 'listStatus' => $empList,'confirmId' => $confirmId,'cameraId' => $cameraId]);
    }
    public function holidayView(Request $request)
    {
        $title = __('hrms.holidays');
        $holidays = $this->holidaysList($request);
        return view("User::HRMS_new.Attendance.holiday")->with(['title' => $title, 'holidays' => $holidays, 'condition' => $this->condition]);
    }

    public function holidaysList(Request $request)
    {
        $api_url = $this->DOMAIN . '/hrms/get-holidays';
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => holidaysList => Method-get ');
        }
    }


    public function updateHoliday(Request $request)
    {
        try {
            $api_url = $this->DOMAIN . '/hrms/holiday';
            $response = $this->helper->postApiCall("put", $api_url, $request->all());
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => updateHoliday => Method-put ');
        }
    }

    public function employeeDetails(Request $request)
    {
        $title = __('hrms.employee_details');
        $family = $this->familyDetails($request);
        $education = $this->educationDetails($request);
        $experience = $this->experienceDetails($request);
        $allDetails = $this->AllDetails($request);
        return view("User::HRMS_new.Employee.employee_details")->with([ 'title' => $title, 'family' => $family, 'education' => $education, 'experience' => $experience, 'All_Details' => $allDetails ]);
    }

    public function AllDetails($data)
    {
        $api_url = $this->DOMAIN . "/hrms/employee-details?employee_id=".$data->id;
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => AllDetails => Method-get ');
        }
    }

    public function familyDetails($data)
    {
        $api_url = $this->DOMAIN . "/hrms/employee-details/family?employeeId=".$data->id;
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => familyDetails => Method-get ');
        }
    }

    public function educationDetails($data)
    {
        $api_url = $this->DOMAIN . "/hrms/employee-details/qualification?employeeId=".$data->id;
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => educationDetails => Method-get ');
        }
    }

    public function experienceDetails($data)
    {
        $api_url = $this->DOMAIN . "/hrms/employee-details/experience?employeeId=".$data->id;
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => experienceDetails => Method-get ');
        }
    }

    public function addFamilyMember(Request $request)
    {
        $validator = $this->familyValidation($request->all());
        if ($validator != []) return $validator;
        $api_url = $this->DOMAIN . "/hrms/employee-details/family";
        try {
            $response = $this->helper->postApiCall("post", $api_url, $request->all());
            return $this->helper->responseHandler($response);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => createAward => Method-post ');
        }
    }

    public function updateFamilyMember(Request $request)
    {
        $validator = $this->familyValidation($request->all());
        if ($validator != []) return $validator;
        $api_url = $this->DOMAIN . '/hrms/employee-details/family';
        try {
            $response = $this->helper->postApiCall("put", $api_url, $request->all());
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => updatePromotion => Method-post ');
        }
    }

    public function removeFamilyMember(Request $request)
    {
        try {
            $api_url = $this->DOMAIN . '/hrms/employee-details/family';
            $response = $this->helper->postApiCall("delete", $api_url, $request->all());
            return $this->helper->responseHandlerWithoutStatusCode($response);

        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' ApiController => deleteEmployeeTransfer => Method-delete ');
        }
    }

    public function addExperience(Request $request)
    {
        $validator = $this->experienceValidation($request->all());
        if ($validator != []) return $validator;
        $api_url = $this->DOMAIN . "/hrms/employee-details/experience";
        try {
            $response = $this->helper->postApiCall("post", $api_url, $request->all());
            return $this->helper->responseHandler($response);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => addExperience => Method-post ');
        }
    }

    public function updateExperience(Request $request)
    {
        $validator = $this->experienceValidation($request->all());
        if ($validator != []) return $validator;
        try {
            $api_url = $this->DOMAIN . '/hrms/employee-details/experience';
            $response = $this->helper->postApiCall("put", $api_url, $request->all());
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => updateExperience => Method-post ');
        }
    }

    public function removeExperience(Request $request)
    {
        try {
            $api_url = $this->DOMAIN . '/hrms/employee-details/experience';
                $response = $this->helper->postApiCall("delete", $api_url, $request->all());
                return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' ApiController => deleteEmployeeTransfer => Method-delete ');
        }
    }

    public function addQualification(Request $request)
    {
        $validator = $this->qualificationValidation($request->all());
        if ($validator != []) return $validator;
        $api_url = $this->DOMAIN . "/hrms/employee-details/qualification";
        try {
            $response = $this->helper->postApiCall("post", $api_url, $request->all());
            return $this->helper->responseHandler($response);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => addExperience => Method-post ');
        }
    }

    public function updateQualification(Request $request)
    {
        $validator = $this->qualificationValidation($request->all());
        if ($validator != []) return $validator;
        try {
            $api_url = $this->DOMAIN . '/hrms/employee-details/qualification';
            $response = $this->helper->postApiCall("put", $api_url, $request->all());
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => updateExperience => Method-post ');
        }
    }

    public function removeQualification(Request $request)
    {
        try {
            $api_url = $this->DOMAIN . '/hrms/employee-details/qualification';
                $response = $this->helper->postApiCall("delete", $api_url, $request->all());
                return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' ApiController => deleteEmployeeTransfer => Method-delete ');
        }
    }

    public function familyValidation($data)
    {
        $response = [];
        $validator = Validator::make($data, [
            'employeeId' => 'required',
            'nameOfFamilyMember' => 'required',
            'age' => 'required|numeric|max:99',
            'relationShipWithEmployee' => 'required',
            'occupation' => 'required',
            'dateOfBirth' => 'required',
            'aadharNo' => 'required|numeric|digits:12',
            'contactNo' => 'nullable|numeric',
            'panNo' => 'required|regex:/^[a-zA-Zء-ي]+[a-zA-Z0-9._]+$/',
        ], [
            'aadharNo.digits' => 'The aadhar number must be 12 digits.',
            'panNo.regex' => 'PAN number should contain Alpha Numberic.',
        ]);
        if ($validator->fails()) {
            $response['code'] = 201;
            $response['msg'] = $validator->errors()->all();
            $response['data'] = null;
        }
        return $response;
    }

    public function experienceValidation($data)
    {
        $response = [];
        $validator = Validator::make($data, [
            'employeeId' => 'required',
            'nameOfCompany' => 'required|regex:/(^([a-zA-Zء-ي]+)?$)/u',
            'designation' => 'required',
            'reportingManager' => 'required|regex:/(^([a-zA-Zء-ي]+)?$)/u',
            'contactOfReportingManager' => 'required|numeric',
            'joiningDate' => 'required',
            'leavingDate' => 'required',
            'reasonForLeaving' => 'required',
            'hrName' => 'required|regex:/(^([a-zA-Zء-ي]+)?$)/u',
            'hrMailId' => 'required|email',
            'hrContactNo' => 'required|numeric',
        ], [
            'nameOfCompany.regex' => 'Name of Company Should Contain only Alphabets.',
//            'designation.regex' => 'Designation Should Contain only Alphabets.',
            'reportingManager.regex' => 'Reporting Manager Should Contain only Alphabets.',
//            'reasonForLeaving.regex' => 'Reason of leaving Should Contain only Alphabets.',
            'hrName.regex' => 'HR name Should Contain only Alphabets.',
        ]);
        if ($validator->fails()) {
            $response['code'] = 201;
            $response['msg'] = $validator->errors()->all();
            $response['data'] = null;
        }
        return $response;
    }

    public function qualificationValidation($data)
    {
        $response = [];
        $validator = Validator::make($data, [
            'employeeId' => 'required',
            'qualificationType' => 'required',
            'nameOfInstitue' => 'required',
            'universityBoard' => 'required',
            'yearOfPassing' => 'required|numeric',
            'percentageGrade' => 'required',
        ], []);
        if ($validator->fails()) {
            $response['code'] = 201;
            $response['msg'] = $validator->errors()->all();
            $response['data'] = null;
        }
        return $response;
    }

    public function getEmployeeLeaves(Request $request)
    {
        $api_url = $this->DOMAIN . "/hrms/employee-leave?start_date=".$request->start_date."&end_date=".$request->end_date;
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => experienceDetails => Method-get ');
        }
    }

    public function leaveByStatus(Request $request)
    {
        $api_url = $this->DOMAIN . "/hrms/leave-by-status?status=" . $request->status . "&month=" . $request->date;
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => leaveByStatus => Method-get ');
        }
    }

    // Function to get the salary revision
    public function getSalaryRevision (Request $request){
        $api_url = $this->DOMAIN . "/hrms/payroll/run-payroll/salary-revision?date=".$request->input('SelectedDate');
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => getSalaryRevision => Method-get ');
        }
    }

    // To get the pay register data
    public function getPayRegister(Request $request)
    {
        $employeeType= (isset($request["EMPLOYEE_TYPE"])) ? $request["EMPLOYEE_TYPE"] : 0;
        $api_url = $this->DOMAIN . "/hrms/payroll/run-payroll/pay-register?date=" . $request->input('SelectedDate') . '&employee_type=' . $employeeType;

        if ($request->components !== '0') {
            $api_url .= '&components=' . $request->components;
        }
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => getSalaryRevision => Method-get ');
        }
    }

    //Employee Self Attendance
    public function employeeAttendance(Request $request)
    {
        $title = "Attendance";
        return view("User::HRMS_new.Employee.employee_attendance")->with(['title' => $title, 'log' => $this->getLogDetails()]);
    }

//  Update Compliance Details
    public function updateComplianceDetail(Request $request)
    {
        $validator = $this->compliancedetailValidation($request->all());
        if ($validator != []) return $validator;
        $api_url = $this->DOMAIN . '/hrms/compliance';
        try {
            $response = $this->helper->postApiCall("put", $api_url, $request->all());
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => updateBankDetail => Method-put ');
        }
    }

//  Download Compliance Details
    public function downloadComplianceDetail(Request $request)
    {
        $employeeType= (isset($request["EMPLOYEE_TYPE"])) ? $request["EMPLOYEE_TYPE"] : 0;
        $api_url = $this->DOMAIN . "/hrms/get-bankdetails?location_id=0&department_id=0&role_id=0&status=1&employee_type=".$employeeType;
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => downloadComplianceDetail => Method-get ');
        }
    }

//  Bulk Update Compliance Details
    public function bulkUpdateComplianceDetail(Request $request)
    {
        $result = [];
        $file = $request->file;
        $pathToStorage = storage_path("complianceDetails");
        if (!file_exists($pathToStorage))
            mkdir($pathToStorage, 0777, true);
        $filename = $file->getClientOriginalName();
        $path = $pathToStorage . "/" . $filename;
        file_put_contents($path, file_get_contents($file->path()));
        $multipartData = array(
            "name" => "file",
            "file" => $path,
        );
        $api_url = $this->DOMAIN . "/hrms/compliance";
        try {
            $response = $this->helper->postApiCall('post', $api_url, $multipartData, true);
            unlink(storage_path('complianceDetails/' . $filename));
            if ($response['data']['code'] == 200) {
                $result['code'] = 200;
                $result['data'] = $response['data']['data'];
                $result['msg'] = $response['data']['message'];
            }  else {
                $result['code'] = $response['data']['code'];
                $result['data'] = $response['data']['data'];
                $result['msg'] = $response['data']['code'] === 400 ? $response['data']['message'] : $response['data']['error'];
            }
            return $result;
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => bulkUpdateComplianceDetail => Method-post ');
        }
    }

    public function compliancedetailValidation($data)
    {
        $response = [];
        $validator = Validator::make($data, [
//            'employee_id' => 'required',
//            'pf_number' => 'required',
//            'esi_number' => 'required',
//            'uan_number' => 'required',
            'ctc' => 'required|numeric',
            'gross' => 'required|numeric',
//            'eligible_pf' => 'required',
//            'pf_scheme' => 'required',
//            'pf_joining' => 'required',
//            'excess_pf' => 'required',
//            'excess_eps' => 'required',
//            'exist_pf' => 'required',
//            'eligible_esi' => 'required',
//            'eligible_pt' => 'required',
//            'pan_number' => 'required',
        ], []);
        if ($validator->fails()) {
            $response['code'] = 201;
            $response['msg'] = $validator->errors()->all();
            $response['data'] = null;
        }
        return $response;
    }

    public function getLogDetails()
    {
        $api_url = $this->DOMAIN . "/hrms/attendance/mark";
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => getLogDetails => Method-get ');
        }
    }

    public function checkInOut(Request $request)
    {
        $api_url = $this->DOMAIN . "/hrms/attendance/mark";
        try {
            $response = $this->helper->postApiCall("post", $api_url, $request->all());
            return $this->helper->responseHandler($response);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => checkInOut => Method-post ');
        }
    }


//  Bulk Update Bank Details
    public function bulkUpdateBankDetail(Request $request)
    {
        $result = [];
        $file = $request->file;
        $pathToStorage = storage_path($request->detailsType."Details");
        if (!file_exists($pathToStorage))
            mkdir($pathToStorage, 0777, true);
        $filename = $file->getClientOriginalName();
        $path = $pathToStorage . "/" . $filename;
        file_put_contents($path, file_get_contents($file->path()));
        $multipartData = array(
            "name" => "file",
            "file" => $path,
        );
        $api_url = $this->DOMAIN . "/hrms/bank-details/bulk-update?detailsType=".$request->detailsType;
        try {
            $response = $this->helper->postApiCall('post', $api_url, $multipartData, true);
            unlink(storage_path($request->detailsType.'Details/' . $filename));
            if ($response['data']['code'] == 200) {
                $result['code'] = 200;
                $result['data'] = $response['data']['data'];
                $result['msg'] = $response['data']['message'];
            }  else {
                $result['code'] = $response['data']['code'];
                $result['data'] = $response['data']['data'];
                $result['msg'] = $response['data']['code'] === 400 ? $response['data']['message'] :  $response['data']['error'];
            }
            return $result;
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => bulkUpdateBankDetail => Method-get ');
        }
    }


    public function downloadBasicDetail(Request $request)
    {
        $employeeType= (isset($request["EMPLOYEE_TYPE"])) ? $request["EMPLOYEE_TYPE"] : 0;
        $api_url = $this->DOMAIN . "/hrms/basic-info?location_id=0&department_id=0&role_id=0&status=1&employee_type=".$employeeType;
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => downloadBasicDetail => Method-get ');
        }
    }

// For pre-requisite details
    public function preRequisiteVIew(Request $request)
    {
        $title = "Pre Requisites";
        $totalCount = 0;
        $locations = (new UserController())->getAllLocations();
        $departments = (new UserController())->getDepartments();
        $users = (new TimeAttendanceController())->users($request);
        $payout_date = (new PfAndEsiController())->getPayRollSettings($request);
        return view("User::HRMS_new.Employee.pre_requisites")->with(['title' => $title,'users' => $users,'locations' => $locations, 'departments' => $departments,'payout_date' => (($payout_date['code']===200) && (isset($payout_date['data']['payoutDate'])))?($payout_date['data']['payoutDate']):('1')]);
    }

    // For pre-requisite details
    public function getCustomSalaryDetails(Request $request)
    {
        $skip = isset($request->skip) ? $request->input('skip') : "0" ;
        $limit = isset($request->SHOW_ENTRIES)  ? isset($request->exportDetails) && $request->exportDetails !== "1" ?  '&limit=' .$request->SHOW_ENTRIES : '&limit=0' : '&limit=0' ;
        $search = isset($request->SearchText)  ?'&name='.$request->SearchText : '' ;
        $employee = isset($request->employeeId)  ?'&employee_id='.$request->employeeId : '' ;
        $employeeType = isset($request->EMPLOYEE_TYPE)  ?'&employee_type='.$request->EMPLOYEE_TYPE : '' ;
        $api_url = $this->DOMAIN . "/hrms/payroll/custom-salary/employees-custom-details?skip=" . $skip . $limit .$search .$employee .$employeeType;
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => getCustomSalaryDetails => Method-get ');
        }
    }

    // To bulk update custom details
    public function customDetailsBulkUpdate (Request $request){
        $result = [];
        $file = $request->file;
        $pathToStorage = storage_path("complianceDetails");
        if (!file_exists($pathToStorage))
            mkdir($pathToStorage, 0777, true);
        $filename = $file->getClientOriginalName();
        $path = $pathToStorage . "/" . $filename;
        file_put_contents($path, file_get_contents($file->path()));
        $multipartData = array(
            "name" => "file",
            "file" => $path,
        );
        $api_url = $this->DOMAIN . "/hrms/payroll/custom-salary/bulk";
        try {
            $response = $this->helper->postApiCall('post', $api_url, $multipartData, true);
            unlink(storage_path('complianceDetails/' . $filename));
            return $this->helper->responseHandlerWithErrorMsg($response);
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => customDetailsBulkUpdate => Method-post ');
        }
    }

    // To edit  custom details in Component and Pre requisites module
    public function editCustomDetails(Request $request)
    {
        $api_url = $this->DOMAIN . "/hrms/payroll/custom-salary/employees-custom-details";
        $data['employee_id'] = $request->employeeId;
        $data['salary_components'] = $request->salaryComponents;
        $data['additional_components'] = $request->additionalComponents;
        $data['deduction_components'] = $request->deductionalComponents;

        try {
            $response = $this->helper->postApiCall('post', $api_url, $data);
            return $this->helper->responseHandler($response);
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => editCustomDetails => Method-post ');
        }
    }

  // To add remove component for the organization in component and pre requisite module
    public function addRemoveOrgComponent(Request $request)
    {
        $api_url = $this->DOMAIN . "/hrms/payroll/custom-salary/org-components";
        $data['remove_components'] = $request->removeComponents;
        $data['new_components'] = $request->addComponents;
        try {
            $response = $this->helper->postApiCall('post', $api_url, $data);
            return $this->helper->responseHandler($response);
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => addRemoveOrgComponent => Method-post ');
        }
    }

    // To add remove component for the organization in component and pre requisite module
    public function getAddRemoveOrgComponent(Request $request)
    {
        $api_url = $this->DOMAIN . "/hrms/payroll/custom-salary/org-components";
        try {
            $response = $this->helper->postApiCall('get', $api_url, 0);
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => getAddRemoveOrgComponent => Method-get ');
        }
    }

    // Attendance Override function call form attendance.js
    public function attendanceOverride(Request $request)
    {
        $api_url = $this->DOMAIN . "/hrms/attendanceOverride";
        $data = $request->all();
        $data['date'] = str_replace(",","-",$request->c_date);
        unset($data['c_date']);

        if($data['leave_id'] === '0')
            unset($data['leave_id']);

        try {
            $response = $this->helper->postApiCall('post', $api_url, $data);
            return $this->helper->responseHandler($response);
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => attendanceOverride => Method-post ');
        }
    }

    // edit Attendance request function call form employee_attendance.js
    public function attendanceRequest(Request $request)
    {
        $rules = array(
            "check_in" => 'required',
            "check_out" => 'required',
            "reason" => 'required',
        );
        $customMessage = [
            'check_in.regex' => __('Check In is required'),
            'check_out.regex' => __('Check Out is required'),
            'reason.regex' => __('Remark is required'),
        ];
        $validator = Validator::make($request->all(), $rules, $customMessage);
        if ($validator->fails()) {
            $error['code'] = 406;
            $error['errors'] =  $validator->errors();
            return $error;
        }
        $api_url = $this->DOMAIN . "/hrms/attendance-request";
        try {
            $response = $this->helper->postApiCall('post', $api_url, $request->all());
            return $this->helper->responseHandler($response);
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => attendanceRequest => Method-post ');
        }
    }
    // addSalaryOnHold
    public function getSalaryOnHold(Request $request)
    {
        $api_url = $this->DOMAIN . "/hrms/payroll/advance-settings/salary-hold?date=".$request->input("date");
        try {
          return $this->helper->postApiCall('get', $api_url, []);
        } catch (\Exception $e) {
            $this->helper->logException("Company", $e->getMessage());
        }
    }

    public function addSalaryOnHold(Request $request)
    {
        try {
            $api_url = $this->DOMAIN . '/hrms/payroll/advance-settings/salary-hold';
            try {
                $data['salary_hold_components'] =[];
                foreach ($request->employeeIds as $key => $value) {
                    array_push($data['salary_hold_components'], (object)[
                        "from" => $request->startDate,
                        "to" => $request->endDate,
                        "employee_id" => $value,
                    ]);
                }
                $response = $this->helper->postApiCall("post", $api_url, $data);
                return $this->helper->responseHandlerWithErrorMsg($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => addSalaryOnHold => Method-post ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => addSalaryOnHold => Method-post ');
        }
    }

    public function updateSalaryOnHold(Request $request)
    {
        try {
            $api_url = $this->DOMAIN . '/hrms/payroll/advance-settings/salary-hold';
            try {
                $data = (object)[
                    "employee_id" => $request->input('empId'),
                    "hold_type" => "pay",
                ];
                $response = $this->helper->postApiCall("put", $api_url, $data);
                return $this->helper->responseHandlerWithoutStatusCode($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => updateSalaryOnHold => Method-put ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => updateSalaryOnHold => Method-put ');
        }
    }

    // function to get employee check-in & check-out time form admin end
    public function getEmployeeAttendance(Request $request)
    {
        $date = explode("-",$request->date);
        $data['date'] = (strlen($date[1]) === 1 )  ? ($date[0] . '0' . ($date[1])) : ($date[0] . '' . ($date[1])) ;

        try {
            $api_url = $this->DOMAIN . '/hrms/attendance?location_id=0&department_id=0&role_id=0&&sortColumn=0&sortOrder=0&skip=0&limit=0&status=1&employee_type=0&employee_id='.$request->id.'&date='.$data['date'];
            if(isset($request->dateRange))
                $api_url .='&start_date='.$request->dateRange[0].'&end_date='.$request->dateRange[1];

            try {
                $response = $this->helper->postApiCall("get", $api_url, []);
                return $this->helper->responseHandlerWithoutStatusCode($response);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => getEmployeeAttendance => Method-get ');
            }
        } catch (\Exception $e) {
            return $this->helper->errorHandler($e, ' EmployeeDetailsController => getEmployeeAttendance => Method-get ');
        }
    }

    // function for common bulk update
    public function commonBulkUpdate(Request $request)
    {
        $result = [];
        $file = $request->file;
        $pathToStorage = storage_path("commonBulkUploadDetails");
        if (!file_exists($pathToStorage))
            mkdir($pathToStorage, 0777, true);
        $filename = $file->getClientOriginalName();
        $path = $pathToStorage . "/" . $filename;
        file_put_contents($path, file_get_contents($file->path()));
        $multipartData = array(
            "name" => "file",
            "file" => $path,
        );
        $data["file"] = $path;
        $api_url = $this->DOMAIN . "/hrms/common-bulk/upload";
        try {
            $response = $this->helper->postApiCall('post', $api_url, $multipartData, true);
            unlink(storage_path('commonBulkUploadDetails/' . $filename));
            if ($response['data']['code'] == 200) {
                $result['code'] = 200;
                $result['data'] = $response['data']['data'];
                $result['msg'] = $response['data']['message'];
            }  else {
                $result['code'] = $response['data']['code'];
                $result['data'] = $response['data']['data'];
                $result['msg'] = $response['data']['code'] === 400 ? $response['data']['message'] :  $response['data']['error'];
            }
            return $result;
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => bulkUpdateBankDetail => Method-get ');
        }
    }

    public function bulkRegisterEmployees(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);
        try {
            $file = $request->file('file');
            $multipartData = [[
                'name' => 'file',
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName(),
                'headers' => [
                    'Content-Type' => $file->getMimeType()
                ]
            ]];
            $api_url = env('MAIN_API') . 'admin/bulk-register';
            $response = $this->helper->postApiCall('post-with-token-multipart', $api_url, $multipartData);
            return response()->json($response['data'], $response['statusCode']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unable to process the file.'], 500);
        }
    }

    public function bulkUpdateEmployees(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);
        try {
            $file = $request->file('file');
            $multipartData = [[
                'name' => 'file',
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName(),
                'headers' => [
                    'Content-Type' => $file->getMimeType()
                ]
            ]];
            $api_url = env('MAIN_API') . 'admin/bulk-update';
            $response = $this->helper->postApiCall('post-with-token-multipart', $api_url, $multipartData);
            return response()->json($response['data'], $response['statusCode']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unable to process the file.'], 500);
        }
    }

    public function downloadBulkRegisterTemplate()
    {
        return $this->downloadTemplateFromApi('admin/bulk-register/template', 'bulk-register-template.xlsx');
    }

    public function downloadBulkUpdateTemplate()
    {
        return $this->downloadTemplateFromApi('admin/bulk-update/template', 'bulk-update-template.xlsx');
    }

    private function downloadTemplateFromApi($path, $filename)
    {
        try {
            $token = Session::get('admin_session')['token'] ?? null;
            if (!$token) {
                abort(401, 'Unauthorized');
            }
            $client = new Client();
            $api_url = rtrim(env('MAIN_API'), '/') . '/' . ltrim($path, '/');
            $response = $client->get($api_url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'user-agent' => request()->userAgent() ?? 'EMPMonitor'
                ]
            ]);
            $content = $response->getBody()->getContents();
            $headers = [
                'Content-Type' => $response->getHeaderLine('Content-Type') ?: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => $response->getHeaderLine('Content-Disposition') ?: 'attachment; filename=' . $filename
            ];
            return response($content, 200, $headers);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unable to download template.'], 500);
        }
    }

    public function myProfileBasicUpdate(Request $request)
    {
        $api_url = $this->DOMAIN . "/hrms/employee-details/requestDetails";
        try {
            $data = $request->all();
            $data[ "type"]=(int)$request->type;
            $response = $this->helper->postApiCall('post', $api_url, $data);
            return $this->helper->responseHandlerData($response);
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => myProfileBasicUpdate => Method-post ');
        }
    }

    public function employeeMyProfile(Request $request)
    {
        $api_url = $this->DOMAIN . "/hrms/employee-details/requestDetails";
        try {
            $data = $request->all();
            $data[ "type"]=(int)$request->type;
            $response = $this->helper->postApiCall('post', $api_url, $data);
            return $this->helper->responseHandlerData($response);
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => employeeMyProfile => Method-post ');
        }
    }

    public function basicDetailsData(Request $request )
    {
        $rules = array(
        "registeredCompanyName" => 'required',
        "brandName" => 'required',
        "director" => 'required',
        "domainName" => 'required',
        "website" => 'required',
        "email" => 'required',
        "contactNumber" => 'required',
        "registeredOfficeAddress" => 'required',
        "corporateOfficeAddress" => 'required',
    );
        $customMessage = [
            'registeredCompanyName.regex' => ('Registered CompanyName is required'),
            'brandName.regex' => ('Brand Name is required'),
            'director.regex' => ('director is required'),
            'domainName.regex' => ('Account Type is required'),
            'website.regex' => ('Website is required'),
            'email.regex' => ('Email is required'),
            'contactNumber.regex' => ('Contact Number is required'),
            'registeredOfficeAddress.regex' => ('Registered Office Address is required'),
            'corporateOfficeAddress.regex' => ('Corporate Office Address is required'),
        ];
        $validator = Validator::make($request->all(), $rules, $customMessage);
        if ($validator->fails()) {
            $error['code'] = 406;
            $error['errors'] = $validator->errors();
            return $error;
        }
        $api_url = $this->DOMAIN . "/hrms/organizationDetails/basicDetails";
        try {
            $response = $this->helper->postApiCall('post', $api_url, $request->all());
            return $this->helper->responseHandlerData($response);
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => basicDetailsData => Method-post ');
        }
    }


    public function getEmailsForDOBNotification(Request $request)
    {
        $api_url = $this->DOMAIN . "/hrms/get-email-alert-birthday";
        try {
            $response = $this->helper->postApiCall('get', $api_url,'');
            return $this->helper->responseHandlerWithoutStatusCode($response);
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => getEmailsForDOBNotification  => Method-get ');
        }
    }

    public function saveEmailsForDOBNotification(Request $request){
        $emailsTo = explode(',', $request->input('emailsTo'));
        $emailsCC = explode(',', $request->input('emailsCC'));
        $emailsBCC = explode(',', $request->input('emailsBCC'));
        $rules = [];
        $customMessages = [];

        foreach ($emailsTo as $key => $email) {
            $data["to_email"][] = $email;
        }
        foreach ($emailsCC as $key => $email) {
            $data["cc_email"][] = $email == "" ? null : $email;
        }
        foreach ($emailsBCC as $key => $email) {
            $data["bcc_email"][] = $email == "" ? null : $email;
        }

        $api_url = $this->DOMAIN . "/hrms/add-email-alert-birthday";
        try {
            $response = $this->helper->postApiCall('post', $api_url, $data);
            return $this->helper->responseHandlerData($response);
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => saveEmailsForDOBNotification => Method-post ');        }
    }

    public function bankDetailsData(Request $request )
    {
        $rules = array(
            "bankName" => 'required',
            "ifsc" => 'required',
            "accountNumber" => 'required',
            "accountType" => 'required',
            "branchName" => 'required',
        );
        $customMessage = [
            'bankName.regex' => ('Bank Name is required'),
            'ifsc.regex' => ('IFSC is required'),
            'accountNumber.regex' => ('Account Number required'),
            'accountType.regex' => ('Account Type is required'),
            'branchName.regex' => ('Branch Name is required'),
        ];
        $validator = Validator::make($request->all(), $rules, $customMessage);
        if ($validator->fails()) {
            $error['code'] = 406;
            $error['errors'] = $validator->errors();
            return $error;
        }
        $api_url = $this->DOMAIN . "/hrms/organizationDetails/bankDetails";
        try {
            $response = $this->helper->postApiCall('post', $api_url, $request->all());
            return $this->helper->responseHandlerData($response);
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => bankDetailsData => Method-post ');
        }
    }
    public function complianceDetailsData(Request $request )
    {
        $rules = array(
            "uan" => 'required',
            "pfJoiningDate" => 'required',
            "excessEPF" => 'required',
            "excessEPS" => 'required',
            "existingPFMember" => 'required',
            "employeeEligibleForPT" => 'required',
            "employeeEligibleForEsi" => 'required',
            "esiNumber" => 'required',
            "pan" => 'required',
            "ctc" => 'required',
            "gross" => 'required',
            "effectiveDate" => 'required',
        );
        $customMessage = [
            'uan.regex' => ('UAN is required'),
            'pfJoiningDate.regex' => ('Pf Joining Date is required'),
            'excessEPF.regex' => ('Excess EPF is required'),
            'excessEPS.regex' => ('Excess EPS is required'),
            'existingPFMember.regex' => ('Existing PF Member is required'),
            'employeeEligibleForPT.regex' => ('Employee Eligible For PT is required'),
            'employeeEligibleForEsi.regex' => ('Employee Eligible For Esi is required'),
            'esiNumber.regex' => ('ESI Number is required'),
            'pan.regex' => ('PAN is required'),
            'ctc.regex' => ('CTC is required'),
            'gross.regex' => ('Gross is required'),
            'effectiveDate.regex' => ('Effective Date is required'),
        ];

        $validator = Validator::make($request->all(), $rules, $customMessage);
        if ($validator->fails()) {
            $error['code'] = 406;
            $error['errors'] = $validator->errors();
            return $error;
        }
        $api_url = $this->DOMAIN . "/hrms/organizationDetails/complianceDetails";
        try {
            $response = $this->helper->postApiCall('post', $api_url, $request->all());
            return $this->helper->responseHandlerData($response);
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController => complianceDetailsData => Method-post ');
        }
    }

    public function adminRequestDetails(Request $request)
    {
        $api_url = $this->DOMAIN . "/hrms/employee-details/requestDetails";
        try {
            $data = $request->all();
            $response = $this->helper->postApiCall('get', $api_url,$data);
            return $this->helper->responseHandlerData($response);
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController =>adminRequestDetails  => Method-get ');
        }
    }

    // get in hand salary
    public function getSalaryInHand(Request $request)
    {
        $api_url = $this->helper->API_URL_3 . "/hrms/payroll/advance-settings/salary-in-hand?limit=1000";
        try {
            return $this->helper->postApiCall('get', $api_url,[]);
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController =>getSalaryInHand  => Method-get ');
        }
    }

    // delete Salary In Hand
    public function deleteSalaryInHand(Request $request)
    {
        $api_url = $this->helper->API_URL_3 . "/hrms/payroll/advance-settings/salary-in-hand";
        try {
            return $this->helper->postApiCall('delete', $api_url, $request->all());

        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController =>deleteSalaryInHand  => Method-delete ');
        }
    }

    // add Salary In Hand
    public function addSalaryInHand(Request $request)
    {
        $api_url = $this->helper->API_URL_3 . "/hrms/payroll/advance-settings/salary-in-hand";
        try {
            $response = $this->helper->postApiCall('post', $api_url, $request->all());
            return $this->helper->responseHandlerData($response);

        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController =>addSalaryInHand  => Method-post ');
        }
    }

    function employeeBiometricSettings(Request $request)
    {
        $data = [];
        if($request->input('custom') === 'customdate'){
            $data['employee_id'] = $request->input('employee_id');
            $data['start_date'] = $request->input('start_date');
            $data['end_date'] = $request->input('end_date');
        } else {
            $data['employee_id'] = $request->input('employee_id');
            $data['custom'] =  $request->input('custom');
        }
        $api_url = $this->helper->API_URL_3 . "/hrms/biometrics";
        try {
            $response = $this->helper->postApiCall('post', $api_url, $data);
            return $response;
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController =>addSalaryInHand  => Method-post ');
        }
    }
    function getEmployeeBiometricSettings(Request $request)
    {
        $api_url = $this->helper->API_URL_3 . "/hrms/biometrics?employee_id=".$request->input('employee_id');
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $response;
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController =>addSalaryInHand  => Method-post ');
        }
    }
    function biometricEnablePassword(Request $request)
    {
        $api_url = $this->helper->API_URL_3 . "/bio-metric/enable-biometric";
        try {
            $data['status'] = $request->input('status');
            $data['secretKey'] = $request->input('secretKey');
            $response = $this->helper->postApiCall('post', $api_url, $data);
            return $response;
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController =>biometricEnablePassword  => Method-post ');
        }
    }
    function biometricSetPassword(Request $request)
    {
        $api_url = $this->helper->API_URL_3 . "/bio-metric/set-password";
        try {
            $data['secretKey'] = $request->input('secretKey');
            $response = $this->helper->postApiCall('post', $api_url, $data);
            return $response;
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController =>addSalaryInHand  => Method-post ');
        }
    }
    function biometricStatus()
    {
        $result = [];
        $api_url = $this->helper->API_URL_3 . "/bio-metric/status";
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            if ($response['code'] === 200) {
                $result = $response;
            } else {
                $result['message'] = $response['message'];
            }
            return $result;
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController =>biometricStatus  => Method-post ');
        }
    }
    function biometricSetEmplist(Request $request)
    {
        $api_url = $this->helper->API_URL_3 . "/hrms/update-bio-metrics-fetch-employee-password-status";
        try {
            $data['status'] = $request->input('status');
            $response = $this->helper->postApiCall('post', $api_url, $data);
            return $response;
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController =>biometricSetEmplist  => Method-post ');
        }
    }
    function biometricEmpListStatus()
    {
        $result = [];
        $api_url = $this->helper->API_URL_3 . "/hrms/get-bio-metric-fetch-employee-password-enable-status";
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            if ($response['code'] === 200) {
                $result = $response;
            } else {
                $result['message'] = $response['message'];
            }
            return $result;
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController =>biometricEmpListStatus  => Method-post ');
        }
    }
    function biometricSetConfirmId(Request $request)
    {
        $api_url = $this->helper->API_URL_3 . "/hrms/update-biometrics_confirmation_status";
        try {
            $data['status'] = $request->input('status');
            $response = $this->helper->postApiCall('post', $api_url, $data);
            return $response;
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController =>biometricEnablePassword  => Method-post ');
        }
    }
    function biometricGetConfirmIdStatus()
    {
        $result = [];
        $api_url = $this->helper->API_URL_3 . "/hrms/get-biometrics_confirmation_status";
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            if ($response['code'] === 200) {
                $result = $response;
            } else {
                $result['message'] = $response['message'];
            }
            return $result;
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController =>biometricStatus  => Method-post ');
        }
    }
    function biometricGetbiometricCameraStatus()
    {
        $result = [];
        $api_url = $this->helper->API_URL_3 . "/hrms/get-camera-overlay-status";
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            if ($response['code'] === 200) {
                $result = $response;
            } else {
                $result['message'] = $response['message'];
            }
            return $result;
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController =>biometricStatus  => Method-post ');
        }
    }
    function biometricCameraStatus(Request $request)
    {
        $api_url = $this->helper->API_URL_3 . "/hrms/update-camera-overlay-status";
        try {
            $data['status'] = $request->input('status');
            $response = $this->helper->postApiCall('post', $api_url, $data);
            return $response;
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController =>biometricEnablePassword  => Method-post ');
        }
    }
    function getEmployeeQRCode(Request $request)
    {
        $result = [];
        $api_url = $this->helper->API_URL_3 . "/bio-metric/qr-code?type=2&data=" . $request->input('id');
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            if ($response['code'] === 200) {
                $result = $response;
            } else {
                $result['message'] = $response['message'];
            }
            return $result;
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController =>biometricStatus  => Method-post ');
        }
    }
    public function mobileSettingsView(Request $request)
    {
        $title = "Mobile Settings";
        $api_url = "https://staging-emp-api-m.empmonitor.com/v1/open-user/get-location-list";
        $data['orgId'] = Session::get($this->helper->getHostName())['token']['organization_id'];
        $response = $this->helper->postApiCall('post', $api_url, $data);
        $locations = $response['data'];
        return view("User::HRMS_new.Attendance.mobileSettings")->with(['title' => $title,'locations' => $locations]);
    }
    public function mobileGeoLocationSettings(Request $request)
    {
        // dd($request->all());
        $data["orgId"] = Session::get($this->helper->getHostName())['token']['organization_id'];
        $data["locationName"] = isset($request->location_Name) ? $request->input('location_Name') : '' ;
        $data["isMobEnabled"] = isset($request->mobileStatus) ? $request->input('mobileStatus') : '' ;
        $data["geoStatus"] = isset($request->geoStatus) ?  $request->input('geoStatus') : '' ;
        $data["latitude"] = isset($request->latitude) ? $request->input('latitude') : '' ;
        $data["longitude"] = isset($request->longitude) ? $request->input('longitude') : '' ;
        $data["radius"] = isset($request->range) ? (int) $request->input('range') : 10 ;
        $api_url = "https://staging-emp-api-m.empmonitor.com/v1/open-user/update-geo-location-details";
        $response = $this->helper->postApiCall('post', $api_url, $data);
        return $response;
    }
    public function getMobileGeoLocationSettings(Request $request)
    {
        $api_url = "https://staging-emp-api-m.empmonitor.com/v1/open-user/get-geo-location-details";
        $data['orgId'] = Session::get($this->helper->getHostName())['token']['organization_id'];
        $data['locationName'] = $request->input('locationName');
        $response = $this->helper->postApiCall('post', $api_url, $data);
       return $response['data'];
    }

    function employeeAccessPointSettings(Request $request)
    {

        $data['employee_id'] = $request->input('employee_id');
        $data['access_point_ids'] = $request->input('access_point_ids');
        $api_url = $this->helper->API_URL_3 . "/hrms/assign-access-point-to-employee";
        try {
            $response = $this->helper->postApiCall('post', $api_url, $data);
            return $response;
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController =>addSalaryInHand  => Method-post ');
        }
    }
    function getAccessPointSettings(Request $request)
    {
        $api_url = $this->helper->API_URL_3 . "/hrms/get-access-point-assigned-to-employee?employee_id=".$request->input('employee_id');
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            return $response;
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController =>addSalaryInHand  => Method-post ');
        }
    }


    public function biometricGetLocations(Request $request)
    {
        $api_url = $this->helper->API_URL_3 . "/hrms/get-biometrics-locations";
        try {
            $response = $this->helper->postApiCall('get', $api_url, []);
            // if($response && $response['code'] == 200 && count($response['data']) > 0){
            //     foreach ($response['data'] as $key => $value) { 
            //         $response['data'][$key]['location_wise_secret_key'] = $this->decryptPswd($value['location_wise_secret_key'], 1, 0);
            //     } 
            // }
            return $response;
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController =>biometricGetLocations  => Method-get ');
        }
    }

    public function biometricUpdateSecretKey(Request $request)
    {
        $api_url = $this->helper->API_URL_3 . "/hrms/update-location-secret-key";
        $data['location_id'] = $request->input('location_id');
        $data['new_secret_key'] = $request->input('new_secret_key');
        try {
            $response = $this->helper->postApiCall('patch', $api_url, $data);
            return $response;
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController =>biometricUpdateSecretKey  => Method-post ');
        }
    }

    function updateLocationWisePinStatus(Request $request)
    {
        $api_url = $this->helper->API_URL_3 . "/hrms/update-location-wise-pin-status";
        try {
            $data['status'] = $request->input('status');
            $response = $this->helper->postApiCall('post', $api_url, $data);
            return $response;
        } catch (\Exception $e) {
            return $this->helper->guzzleErrorHandler($e, ' EmployeeDetailsController =>updateLocationWisePinStatus  => Method-post ');
        }
    }


    public function decryptPswd($password)
    {
        $encryptionMethod = env('PASSWORD_ALGORITHM'); // It's an algorithm used
        $secret = env('CRYPTO_PASSWORD');  // The secret key from env file
        $iv = env('PASSWORD_IV');  // IV for the matching the password
         $decryptedMessage = openssl_decrypt($password, $encryptionMethod, $secret, 0, $iv);
         return $decryptedMessage;
    }

    
}


