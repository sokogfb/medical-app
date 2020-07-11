<?php

namespace App\Http\Livewire;

use App\Http\Controllers\API\ProcessController;
use App\Symptom;
use App\User;
use Exception;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Welcome extends Component
{
    public $name;
    public $email;
    public $year_of_birth;
    public $user_id;
    public $genderTypes = array('male', 'female', 'others');
    public $gender;
    public $allSymptoms;
    public $readyToLoad;
    public $patientSymptoms = [];
    public $symptoms;

    public function mount()
    {
        $this->readyToLoad = false;
    }

    /**
     * updated function
     * validates in real time
     * @param $field
     * @throws ValidationException
     */

    public function updated($field)
    {
        $this->validateOnly($field, [
            'symptoms' => ['nullable', 'array'],
            'year_of_birth' => ['nullable', 'numeric'],
            'name' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'max:255', 'in:male,female,others'],
            'symptom' => ['nullable', 'numeric',],
            'email' => ['nullable', 'email', 'string', 'max:255', 'unique:users'],
        ]);
    }

    /**
     * save user
     */
    public function saveUser()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'string', 'max:255', 'unique:users'],
            'gender' => ['required', 'max:255', 'in:male,female,others'],
            'year_of_birth' => ['required', 'numeric',],
        ]);

        if (isset($this->year_of_birth)) {
            $this->resetErrorBag('year_of_birth');
        } else {
            $this->addError('year_of_birth', 'Please select year of birth.');
        }

        $user = User::query()->create([
            'name' => $this->name,
            'email' => $this->email,
            'gender' => $this->gender,
            'year_of_birth' => $this->year_of_birth,
            'password' => bcrypt($this->email),
        ]);

        $this->reset();

        if ($user)
            $this->readyToLoad = true;
        $this->user_id = $user->id;
        session()->flash('success', 'Patient ' . $this->name . ' has been created, Choose the symptoms.');
    }

    /**
     * store symptoms
     * @return void
     */
    public function storeSymptom()
    {
        $this->validate([
            'allSymptoms' => ['required', 'numeric'],
        ]);

        $symptom = null;

        foreach ((new ProcessController())->fetchSymptom($this->allSymptoms) as $value) {
            $symptom = Symptom::query()->where('user_id', $this->user_id)
                ->where('symptomID', $value->ID)
                ->where('is_processed', false)
                ->first();
            if (!$symptom)
                $symptom = Symptom::query()->create([
                    'user_id' => $this->user_id,
                    'symptomID' => $value->ID,
                    'symptomName' => $value->Name,
                ]);
        }
        $this->loadSymptoms();
        session()->flash('success', 'Symptom ' . $symptom->symptomName . ' has been created.');
    }

    /**
     * load the stored symptoms
     */
    public function loadSymptoms()
    {
        $this->patientSymptoms = Symptom::query()
            ->where('user_id', $this->user_id)
            ->where('is_processed', false)
            ->get();
    }

    /**
     * remove symptom
     * @param string $id
     * @throws Exception
     */
    public function removeSymptom(string $id)
    {
        $symptom = Symptom::query()->findOrFail($id);
        $symptom->delete();
        $this->loadSymptoms();
        session()->flash('success', 'Symptom ' . $symptom->symptomName . ' has been removed.');
    }

    /**
     * diagnose
     */
    public function diagnose()
    {
//        $this->validate([
//            'symptoms' => ['required', 'array'],
//        ]);

        dd($this->symptoms);

    }


    public function render()
    {
        return view('livewire.welcome');
    }
}
