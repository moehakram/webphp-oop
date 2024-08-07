<?php

namespace  App\Service;

use Exception;
use App\Domain\User;
use MA\PHPQUICK\Collection;
use App\Repository\UserRepository;
use MA\PHPQUICK\Database\Database;
use MA\PHPQUICK\Exception\ValidationException;
use App\Models\User\{UserRegisterRequest, UserLoginRequest, UserProfileUpdateRequest, UserPasswordUpdateRequest};
use MA\PHPQUICK\Token;
use MA\PHPQUICK\Validation\InputHandler;

class UserService
{

    private UserRepository $userRepository;

    const expireActivationEmail =  1 * 24  * 60 * 60;
    const token_secret_jwt =  'fe1ed383b50832081d04bef6067540efffaaa54c66066a83cc1cf994af07883359012';

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(UserRegisterRequest $request): User
    {
        if ($request->validate()) {
            throw new ValidationException('errors', $request->getErrors());
        }

        try {
            Database::beginTransaction();
            $user = new User();
            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);
            $user->role = 0;
            $user->is_active = 0;
            $user->activated_at = null;
            $data = $this->userRepository->save($user);
            $this->sendActivationEmail($data);
            Database::commitTransaction();
            return $user;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }


    public function sendActivationEmail(User $user): void
    {
        $token = new Token(self::token_secret_jwt, [
            'email' => $user->email,
            'expiry' => time() + self::expireActivationEmail
        ]);

        // create the activation link
        $activation_link = sprintf(config('app.url') . "/users/activate?activation_code=%s", $token->generateToken());
        write_log("activation_link : " . $activation_link);

        // $subject = 'Please activate your account';
        // $message = <<<MESSAGE
        //         Hi,
        //         Please click the following link to activate your account:
        //         $activation_link
        //         MESSAGE;
        // // email header
        // $header = "From:" . SENDER_EMAIL_ADDRESS;

        // send the email
        // mail($email, $subject, nl2br($message), $header);

    }

    public function activationAccount(string $activation_code): bool
    {

        $token = new Token(self::token_secret_jwt);
        if (!$token->verifyToken($activation_code)) {
            throw new ValidationException('Ativation code tidak valid');
        }
        
        $user = $this->userRepository->findByEmail($token->email);
        if ($token->expiry > time()) {
            return $this->activateUserIfNotActive($user);
        } else {
            return $this->handleExpiredToken($user, $token);
        }
    }

    private function activateUserIfNotActive($user): bool
    {
        if ($user->is_active === 0) {
            $this->userRepository->activateUser($user->id);
            write_log([
                'username' => $user->username,
                'status' => 'sudah active'
            ]);
            return true;
        } else {
            write_log([
                'username' => $user->username,
                'status' => 'sudah active sejak: ' . $user->activated_at
            ]);
            return true;
        }
    }

    private function handleExpiredToken($user, $token): bool
    {
        if ($user->is_active === 1) {
            write_log([
                'username' => $user->username,
                'status' => 'sudah active sejak: ' . $user->activated_at
            ]);
            return true;
        }
        
        $this->userRepository->deleteById($user->id);
        throw new ValidationException('Activation code sudah kedaluarsa, silakan daftarkan kembali.');
    }


    public function login(UserLoginRequest $request): User
    {
        if ($request->validate()) {
            throw new ValidationException("Id and Password can not blank");
        }

        $user = $this->userRepository->findByUsername($request->username);
        if ($user == null) {
            throw new ValidationException("Id or password Anda salah !");
        }
        
        if (!password_verify($request->password, $user->password)) {
            throw new ValidationException("Id or password Anda Salah !");
        }

        return $user;
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
            throw new ValidationException("Id, Name can not blank", new Collection($errors));
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

    public function getUser($id){
        return $this->userRepository->findById($id);
    }
}
