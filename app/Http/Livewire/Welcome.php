<?php

namespace App\Http\Livewire;

use App\Diagnose;
use App\Entry;
use App\Http\Controllers\API\ProcessController;
use App\Symptom;
use App\User;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Welcome extends Component
{
    public $name;
    public $email;
    public $year_of_birth;
    public $genderTypes;
    public $gender;
    public $entry_id;
    public $allSymptoms;
    public $readyToLoad;
    public $show_diagnosis;
    public $patientSymptoms;
    public $patientDiagnosis;
    public $viewPatients;
    public $patients;

    public function mount()
    {
        $this->genderTypes = array('male', 'female');
        $this->readyToLoad = $this->show_diagnosis = $this->viewPatients = false;
        $this->patientSymptoms = $this->patientDiagnosis = $this->patients = [];
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

        $this->reset('name', 'email', 'gender', 'year_of_birth');

        // create entry number
        $entry = Entry::query()->create([
            'user_id' => $user->id,
            'entryNumber' => Str::random(8)
        ]);

        if ($user)
            $this->readyToLoad = true;
        $this->entry_id = $entry->id;
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
            $symptom = Symptom::query()
                ->where('entry_id', $this->entry_id)
                ->where('symptomID', $value->ID)
                ->where('is_processed', false)
                ->first();
            if (!$symptom)
                $symptom = Symptom::query()->create([
                    'entry_id' => $this->entry_id,
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
            ->latest()
            ->with('entry')
            ->where('entry_id', $this->entry_id)
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
        session()->flash('info', 'Symptom ' . $symptom->symptomName . ' has been removed.');
    }

    /**
     * diagnose
     */
    public function diagnose()
    {
        $symptoms = Symptom::query()
            ->with('entry')
            ->where('entry_id', $this->entry_id)
            ->where('is_processed', false)
            ->get();

        // get the user/patient
        $user = $symptoms->first()->entry->user;

        foreach ($symptoms as $symptom) {
            // send diagnosis
            $results = (new ProcessController())->diagnosis(
                $symptom->symptomID,
                $user->gender,
                $user->year_of_birth
            );

            foreach ($results as $result) {
                Diagnose::query()->create([
                    'entry_id' => $this->entry_id,
                    'symptom_id' => $symptom->id,
                    'issueID' => $result->Issue->ID,
                    'name' => $result->Issue->Name,
                    'issueDescription' => $result->Issue->IcdName,
                    'accuracy' => $result->Issue->Accuracy,
                    'diagnosis' => $result,
                ]);
            }

            // update symptom to processed
            $symptom->update([
                'is_processed' => true
            ]);
        }

        $this->show_diagnosis = true;
        session()->flash('success', 'All symptoms were diagnosed successfully');
        $this->loadDiagnosis();
    }

    /**
     * load all diagnosis
     */
    public function loadDiagnosis()
    {
        $this->patientDiagnosis = Diagnose::query()
            ->latest()
            ->with('entry', 'symptom')
            ->where('entry_id', $this->entry_id)
            ->get();
    }

    /**
     * mark as valid
     * @param string|null $id
     * @throws Exception
     */
    public function markDiagnosisAsValid(string $id)
    {
        $diagnosis = Diagnose::query()
            ->findOrFail($id);
        $diagnosis->update([
            'is_valid' => true
        ]);
        $this->loadDiagnosis();
        session()->flash('success', 'Diagnosis ' . $diagnosis->name . ' has been marked as valid.');

    }

    /**
     * continue
     */
    public function proceedToPatients()
    {
        // check if the any valid records exists
        $valid = Diagnose::query()
            ->latest()
            ->with('entry', 'symptom')
            ->where('entry_id', $this->entry_id)
            ->where('is_valid', true)
            ->get();

        // check if the any in valid records exists
        $inValid = Diagnose::query()
            ->latest()
            ->with('entry', 'symptom')
            ->where('entry_id', $this->entry_id)
            ->where('is_valid', false)
            ->get();

        if (count($valid)) {
            if (count($inValid)) {
                // loop and remove the issue and symptom
                foreach ($inValid as $diagnose) {
                    $diagnose->symptom->delete();
                    $diagnose->delete();
                }
                $this->loadPatients();
            }
        } else {
            session()->flash('warning', 'Sorry! We can\'t proceed until you mark at least one issue as valid diagnosis.');
        }
    }

    /**
     * cancel and continue
     */
    public function cancelAndContinue()
    {
        $entry = Entry::query()
            ->latest()
            ->with('symptom', 'diagnose')
            ->firstWhere('id', $this->entry_id);

        // delete all symptoms
        foreach ($entry->symptom as $symptom) {
            $symptom->delete();
        }

        // delete all diagnosis
        foreach ($entry->diagnose as $diagnose) {
            $diagnose->delete();
        }

        // delete the entry
        $entry->delete();
        $this->loadPatients();
    }

    /**
     * load patients
     */
    public function loadPatients()
    {
        $this->viewPatients = true;
        $this->patients = User::query()
            ->with('entry')
            ->latest()
            ->get();
    }


    public function render()
    {
        return view('livewire.welcome');
    }
}
