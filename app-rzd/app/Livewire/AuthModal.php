<?php

namespace App\Livewire;

use Livewire\Component;

class AuthModal extends Component
{
    public function render()
    {
        return view('livewire.auth-modal');
    }
}

// <?php

// namespace App\Livewire;

// use Livewire\Component;

// class AuthModal extends Component
// {
//     public $showModal = false;
//     public $isLogin = true;
    
//     // Данные для входа
//     public $loginEmail = '';
//     public $loginPassword = '';
    
//     // Данные для регистрации
//     public $registerEmail = '';
//     public $registerLogin = '';
//     public $registerPassword = '';
//     public $registerPasswordConfirmation = '';
//     public $registerLastName = '';
//     public $registerFirstName = '';
//     public $registerPhone = '';

//     protected $rules = [
//         'loginEmail' => 'required|email',
//         'loginPassword' => 'required',
        
//         'registerEmail' => 'required|email|unique:users,email',
//         'registerLogin' => 'required|unique:users,login',
//         'registerPassword' => 'required|min:6|confirmed',
//         'registerLastName' => 'required',
//         'registerFirstName' => 'required',
//         'registerPhone' => 'required'
//     ];

//     public function toggleModal()
//     {
//         $this->showModal = !$this->showModal;
//     }

//     public function switchToRegister()
//     {
//         $this->isLogin = false;
//     }

//     public function switchToLogin()
//     {
//         $this->isLogin = true;
//     }

//     public function login()
//     {
//         $this->validate([
//             'loginEmail' => 'required|email',
//             'loginPassword' => 'required'
//         ]);

//         // Логика входа
//         if (auth()->attempt(['email' => $this->loginEmail, 'password' => $this->loginPassword])) {
//             $this->showModal = false;
//             $this->resetForm();
//             session()->flash('message', 'Вы успешно вошли в систему!');
//         } else {
//             session()->flash('error', 'Неверные учетные данные.');
//         }
//     }

//     public function register()
//     {
//         $this->validate([
//             'registerEmail' => 'required|email|unique:users,email',
//             'registerLogin' => 'required|unique:users,login',
//             'registerPassword' => 'required|min:6|confirmed',
//             'registerLastName' => 'required',
//             'registerFirstName' => 'required',
//             'registerPhone' => 'required'
//         ]);

//         // Логика регистрации
//         $user = User::create([
//             'email' => $this->registerEmail,
//             'login' => $this->registerLogin,
//             'password' => bcrypt($this->registerPassword),
//             'last_name' => $this->registerLastName,
//             'first_name' => $this->registerFirstName,
//             'phone' => $this->registerPhone
//         ]);

//         auth()->login($user);
//         $this->showModal = false;
//         $this->resetForm();
//         session()->flash('message', 'Регистрация прошла успешно!');
//     }

//     private function resetForm()
//     {
//         $this->reset(['loginEmail', 'loginPassword', 'registerEmail', 'registerLogin', 
//                      'registerPassword', 'registerPasswordConfirmation', 
//                      'registerLastName', 'registerFirstName', 'registerPhone']);
//     }

//     public function render()
//     {
//         return view('livewire.auth-modal');
//     }
// }
