<?php

namespace  App\Service;

use MA\PHPQUICK\Database\Database;
use App\Repository\UserRepository;
use MA\PHPQUICK\Exception\ValidationException;
use App\Domain\User;
use Exception;
use App\Models\User\{UserRegisterRequest, UserLoginRequest, UserProfileUpdateRequest, UserPasswordUpdateRequest};

class UserService
{

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(UserRegisterRequest $request): User
    {
        // $this->validateUserRegistrationRequest($request);

        try {
            Database::beginTransaction();

            if(!$request->validate()){
                throw new ValidationException('error',$request->getErrors());
            }
            // $user = $this->userRepository->findById($request->id);
            // if ($user != null) {
            //     throw new ValidationException("User Id already exists");
            // }
            $user = new User();
            $user->id = $request->id;
            $user->name = $request->name;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);
            $user->role = 0;

            $this->userRepository->save($user);

            Database::commitTransaction();
            return $user;
        } catch (ValidationException $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserRegistrationRequest(UserRegisterRequest $request)
    {
        if (
            $request->id == null || $request->name == null || $request->password == null ||
            trim($request->id) == "" || trim($request->name) == "" || trim($request->password) == ""
        ) {
            throw new ValidationException("Id, Name, Password can not blank");
        }
    }

    public function login(UserLoginRequest $request): User
    {
       if(!$request->validate()){
            throw new ValidationException("Id and Password can not blank");
       }

        $user = $this->userRepository->findById($request->id);
        if ($user == null) {
            throw new ValidationException("Id or password Anda salah !");
        }

        if (password_verify($request->password, $user->password)) {
            return $user;
        } else {
            throw new ValidationException("Id or password Anda Salah !");
        }
    }

    public function updateProfile(UserProfileUpdateRequest $request): User
    {
        $this->validateUserProfileUpdateRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->id);
            if ($user == null) {
                throw new ValidationException("User is not found");
            }

            $user->name = $request->name;
            $this->userRepository->update($user);

            Database::commitTransaction();
            return $user;
        } catch (Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserProfileUpdateRequest(UserProfileUpdateRequest $request)
    {
        $errors = [];

        if ($request->id == null || trim($request->id) == "") {
            $errors['id'] = "Id cannot be blank";
        }

        if ($request->name == null || trim($request->name) == "") {
            $errors['name'] = "Name cannot be blank";
        }

        if (!empty($errors)) {
            throw new ValidationException("Id, Name can not blank", $errors);
        }
    }

    public function updatePassword(UserPasswordUpdateRequest $request): User
    {
        $this->validateUserPasswordUpdateRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->id);
            if ($user == null) {
                throw new ValidationException("User is not found");
            }

            if (!password_verify($request->oldPassword, $user->password)) {
                throw new ValidationException("Old password is wrong");
            }

            $user->password = password_hash($request->newPassword, PASSWORD_BCRYPT);
            $this->userRepository->update($user);

            Database::commitTransaction();
            return $user;
        } catch (Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserPasswordUpdateRequest(UserPasswordUpdateRequest $request)
    {
        if (
            $request->id == null || $request->oldPassword == null || $request->newPassword == null ||
            trim($request->id) == "" || trim($request->oldPassword) == "" || trim($request->newPassword) == ""
        ) {
            throw new ValidationException("Id, Old Password, New Password can not blank");
        }
    }
}
