<?php

namespace App\Services\_Auth;

use App\Models\User;
use App\Services\_Abstract\BaseService;
use App\Services\_Constant\ConstantService;
use Illuminate\Support\Facades\Auth;
class AuthService extends BaseService
{
    protected $userModel;
    protected $commonService;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    public function login($input)
    {
        $password = md5($input['password']);
        
        $user = $this->userModel->where('name', $input['name'])->where('password', $password)->first();
        if (!$user) {
            return $this->sendErrorResponse('Unauthorized', ConstantService::HTTP_UNAUTHORIZED);
        }
        $token = auth(ConstantService::AUTH_USER)->login($user);
        $user = auth(ConstantService::AUTH_USER)->user();
        $data = [
            'token' => $token,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];
        return $this->sendSuccessResponse($data);
    }

    public function logout()
    {
        auth(ConstantService::AUTH_USER)->logout();
        return $this->sendSuccessResponse([]);
    }

    public function changePassword($request)
    {
        $user = $this->userModel->find(auth(ConstantService::AUTH_USER)->user()->id);
        if (md5($request['password']) != auth(ConstantService::AUTH_USER)->user()->password) {
            return $this->sendErrorResponse('password not match', ConstantService::HTTP_FORBIDDEN);
        }

        $newPassword = md5($request['new_password']);
        $user->password = $newPassword;
        $result = $user->update();
        return $this->sendSuccessResponse($result);
    }
}
