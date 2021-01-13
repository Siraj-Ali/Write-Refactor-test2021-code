<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $repository;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->repository = $bookingRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if($user_id = $request->get('user_id')) {

            $response = $this->repository->getUsersJobs($user_id);

        }
        elseif($request->__authenticatedUser->user_type == env('ADMIN_ROLE_ID') || $request->__authenticatedUser->user_type == env('SUPERADMIN_ROLE_ID'))
        {
            $response = $this->repository->getAll($request);
        }

         // for vue or other front end framework we need and recommanded way to send response like this :
         return response([
             'status' => true,
             'response' => $response
          ], Response::HTTP_OK);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $job = $this->repository->with('translatorJobRel.user')->find($id);

        //return response($job);

        // for vue or other front end framework we need and recommanded way to send response like this :
         return response([
             'status' => true,
             'response' => $job
          ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $response = $this->repository->store($request->__authenticatedUser, $request->all());

        //return response($response);

       // for vue or other front end framework we need and recommanded way to send response like this :
        return response([
             'status' => true,
             'response' => $response
          ], Response::HTTP_OK);

    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        $data = $request->all();
        $cuser = $request->__authenticatedUser;
        $response = $this->repository->updateJob($id, array_except($data, ['_token', 'submit']), $cuser);

         // for vue or other front end framework we need and recommanded way to send response like this :
         return response([
              'status' => true,
              'response' => $response
           ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        $adminSenderEmail = config('app.adminemail');

        $response = $this->repository->storeJobEmail($request->all());

        //return response($response);
// for vue or other front end framework we need and recommanded way to send response like this :
        return response([
            'status' => true,
            'response' => $response
         ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        if($user_id = $request->get('user_id')) {

            $response = $this->repository->getUsersJobsHistory($user_id, $request);

           // for vue or other front end framework we need and recommanded way to send response like this :
         return response([
            'status' => true,
            'response' => $response
         ], Response::HTTP_OK);
        }

        return null;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        $user = $request->__authenticatedUser;

        $response = $this->repository->acceptJob($request->all(), $user);

 // for vue or other front end framework we need and recommanded way to send response like this :
        return response([
            'status' => true,
            'response' => $response
         ], Response::HTTP_OK);
        //return response($response);
    }

    public function acceptJobWithId(Request $request)
    {
        $data = $request->get('job_id');
        $user = $request->__authenticatedUser;

        $response = $this->repository->acceptJobWithId($data, $user);

        // return response($response);

        // for vue or other front end framework we need and recommanded way to send response like this :
            return response([
                'status' => true,
                'response' => $response
             ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        $user = $request->__authenticatedUser;

        $response = $this->repository->cancelJobAjax($request->all(), $user);

        // return response($response);

        // for vue or other front end framework we need and recommanded way to send response like this :
            return response([
                'status' => true,
                'response' => $response
             ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        $data = $request->all();

        $response = $this->repository->endJob($data);

        // return response($response);

        // for vue or other front end framework we need and recommanded way to send response like this :
            return response([
                'status' => true,
                'response' => $response
             ], Response::HTTP_OK);

    }

    public function customerNotCall(Request $request)
    {
        $data = $request->all();

        $response = $this->repository->customerNotCall($data);

        return response($response);

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        $user = $request->__authenticatedUser;

        $response = $this->repository->getPotentialJobs($user);

        // for vue or other front end framework we need and recommanded way to send response like this :
            return response([
                'status' => true,
                'response' => $response
             ], Response::HTTP_OK);
    }

    public function distanceFeed(Request $request)
    {
        if (isset($request->distance) && $request->distance != "") {
            $distance = $request->distance;
        } else {
            $distance = "";
        }
        if (isset($request->time) && $request->time != "") {
            $time = $request->time;
        } else {
            $time = "";
        }
        if (isset($request->jobid) && $request->jobid != "") {
            $jobid = $request->jobid;
        }

        if (isset($request->session_time) && $request->session_time != "") {
            $session = $request->session_time;
        } else {
            $session = "";
        }

        if ($request->flagged == 'true') {
            if($request->admincomment == '') return "Please, add comment";
            $flagged = 'yes';
        } else {
            $flagged = 'no';
        }
        
        if ($request->manually_handled == 'true') {
            $manually_handled = 'yes';
        } else {
            $manually_handled = 'no';
        }

        if ($request->by_admin == 'true') {
            $by_admin = 'yes';
        } else {
            $by_admin = 'no';
        }

        if (isset($request->admincomment) && $request->admincomment != "") {
            $admincomment = $request->admincomment;
        } else {
            $admincomment = "";
        }
        if ($time || $distance) {

            $affectedRows = Distance::where('job_id', '=', $jobid)->update(array('distance' => $distance, 'time' => $time));
        }

        if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {

            $affectedRows1 = Job::where('id', '=', $jobid)->update(array('admin_comments' => $admincomment, 'flagged' => $flagged, 'session_time' => $session, 'manually_handled' => $manually_handled, 'by_admin' => $by_admin));

        }

         // for vue or other front end framework we need and recommanded way to send response like this :
            return response([
                'status' => true,
                'response' => 'Record Updated'
             ], Response::HTTP_OK);
    }

    public function reopen(Request $request)
    {
        $response = $this->repository->reopen($request);

       // for vue or other front end framework we need and recommanded way to send response like this :
        return response([
            'status' => true,
            'response' => $response
         ], Response::HTTP_OK);
    }

    public function resendNotifications(Request $request)
    {
        $job = $this->repository->find($request->jobid);
        $job_data = $this->repository->jobToData($job);
        $this->repository->sendNotificationTranslator($job, $job_data, '*');

        // for vue or other front end framework we need and recommanded way to send response like this :
            return response([
                'status' => true,
                'success' => 'Push sent'
             ], Response::HTTP_OK);
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {
        $job = $this->repository->find($request->jobid);
        $job_data = $this->repository->jobToData($job);

        try {
            $this->repository->sendSMSNotificationToTranslator($job);
            return response(['success' => 'SMS sent']);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }
    }

}
