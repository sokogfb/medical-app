<div class="container-fluid">
    <div class="text-center text-uppercase">
        <hr>
        <h1>{{ config('app.name') }}</h1>
        <div wire:loading>
            <img src="{{ asset('img/loading.gif') }}" alt="" style="width: 100px!important;">
            <p><b><i>-- Processing --</i></b></p>
        </div>
        <div wire:loading.attr="hidden">
            <h1>{{ \Faker\Factory::create()->emoji }}</h1>
        </div>
        <hr>
    </div>

    @if($viewPatients)
        <div class="row">
            <div class="col-md-12">
                <br>
                <br>
                <div class="card">
                    <div class="card-header bg-success text-center text-uppercase"><b>PATIENT(S) LIST</b>
                        <button type="button" wire:click="loadPatients" wire:loading.attr="disabled"
                                class="btn btn-link text-white float-right"><b>Reload</b>
                        </button>
                    </div>
                    <div class="card-body">
                        @include('inc.alert')
                        <div wire:poll.60000ms="loadPatients">{{--Reloads after every 2 minutes--}}
                            @if(count($patients))
                                <table class="table-responsive table table-hover col-md-12">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Patient Name</th>
                                        <th>Patient Email</th>
                                        <th>Patient Gender</th>
                                        <th>Patient Age</th>
                                        <th>Entry Level(s)</th>
                                        <th>Created On</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($count = 1)
                                    @foreach($patients as $patient)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $patient->name }}</td>
                                            <td><a href="mailto:{{ $patient->email }}">{{ $patient->email }}</a></td>
                                            <td>{{ $patient->gender }}</td>
                                            <td>{{ \App\Http\Controllers\SystemController::getAge($patient->year_of_birth) }}</td>
                                            <td><button class="btn btn-outline-primary">View [ {{ number_format(count($patient->entry)) }} ]</button></td>
                                            <td>{{ date('F d, Y', strtotime($patient->created_at)) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <hr>
                                <div class="form-group">
                                    <a href="{{ url('/') }}" wire:loading.attr="disabled"
                                       class="btn btn-outline-success float-right">START NEW DIAGNOSIS
                                    </a>
                                </div>
                            @else
                               <center>
                                   <hr>
                                   <h4 class="text-center text-info"><b>No patients were found.</b></h4>
                                   <a href="{{ url('/') }}" wire:loading.attr="disabled"
                                      class="btn btn-outline-success">START NEW DIAGNOSIS
                                   </a>
                                   <hr>
                               </center>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        @if(!$show_diagnosis)
            <div class="row">
                <div class="col-md-1">&nbsp;</div>
                <div class="col-md-4">
                    <div class="card shadow">
                        <div class="card-header text-uppercase">
                            <b>{{ !$readyToLoad ? 'Patient Details' : 'ApiMedic Symptoms' }}</b></div>
                        <div class="card-body">
                            @if(!$readyToLoad)
                                <form wire:submit.prevent="saveUser" method="post">
                                    <div class="form-group">
                                        <label for="name">Full Name</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                               wire:model.lazy="name" id="name" required>
                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                               wire:model.lazy="email" id="email" required>
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="gender">Gender</label>
                                        <select class="form-control @error('gender') is-invalid @enderror"
                                                wire:model.lazy="gender" id="gender" required>
                                            <option value="gender" selected disabled>Select Gender</option>
                                            @foreach($genderTypes as $genderType)
                                                <option
                                                    value="{{ $genderType }}">{{ ucfirst(\Illuminate\Support\Str::lower($genderType)) }}</option>
                                            @endforeach
                                        </select>
                                        @error('gender')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="year_of_birth">Year Of Birth</label>
                                        @php($start = env('START_YEAR'))
                                        @php($end = intval(date('Y')))
                                        <select class="form-control @error('year_of_birth') is-invalid @enderror"
                                                wire:model.lazy="year_of_birth" id="year_of_birth">
                                            <option selected disabled>Year Of
                                                Birth
                                            </option>
                                            @for($i = $start; $i <= $end; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                        @error('year_of_birth')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <button wire:click="saveUser" wire:loading.attr="disabled" type="submit"
                                                class="btn btn-primary float-right">SAVE
                                        </button>
                                    </div>
                                </form>
                            @else
                                @include('inc.alert')
                                <form wire:submit.prevent="storeSymptom" method="post">
                                    <div class="form-group">
                                        <label for="allSymptoms">Select Symptom</label>
                                        <select class="form-control @error('allSymptoms') is-invalid @enderror"
                                                wire:model.lazy="allSymptoms" required>
                                            <option value="allSymptoms" selected disabled>Select Symptom</option>
                                            @foreach((new \App\Http\Controllers\API\ProcessController())->symptoms() as $symptom)
                                                <option
                                                    value="{{ $symptom->ID }}">{{ $symptom->Name }}</option>
                                            @endforeach
                                        </select>
                                        @error('allSymptoms')
                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <button wire:click="storeSymptom" wire:loading.attr="disabled" type="submit"
                                                class="btn btn-primary float-right">ADD
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
                @if($readyToLoad)
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-danger text-uppercase"><b>Symptoms Selected</b>
                                <button type="button" wire:click="loadSymptoms" wire:loading.attr="disabled"
                                        class="btn btn-link text-white float-right"><b>Reload</b>
                                </button>
                            </div>
                            <div class="card-body">
                                <div wire:poll.60000ms="loadSymptoms">{{--Reloads after every 2 minutes--}}
                                    @if(count($patientSymptoms))
                                        <table class="table-responsive table table-hover col-md-12">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Entry NO</th>
                                                <th>Symptom ID</th>
                                                <th>Symptom Name</th>
                                                <th>Created</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php($count = 1)
                                            @foreach($patientSymptoms as $patientSymptom)
                                                <tr>
                                                    <td>{{ $count++ }}</td>
                                                    <td>{{ $patientSymptom->entry->entryNumber }}</td>
                                                    <td>{{ $patientSymptom->symptomID }}</td>
                                                    <td>{{ $patientSymptom->symptomName }}</td>
                                                    <td>{{ \App\Http\Controllers\SystemController::elapsedTime($patientSymptom->created_at) }}</td>
                                                    <td>
                                                        <button type="button"
                                                                wire:click="removeSymptom('{{ $patientSymptom->id }}')"
                                                                wire:loading.attr="disabled"
                                                                class="btn btn-danger btn-sm">
                                                            REMOVE
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <hr>
                                        <div class="form-group">
                                            <button wire:click="diagnose" wire:loading.attr="disabled" type="submit"
                                                    class="btn btn-outline-success float-right">DIAGNOSE SYMPTOMS
                                            </button>
                                        </div>
                                    @else
                                        <hr>
                                        <h4 class="text-center text-info"><b>No Symptoms have been selected.</b></h4>
                                        <hr>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-md-7">
                        <center>
                            <img src="{{ asset('img/patient.gif') }}" alt="">
                            <br>
                            <br>
                            <button type="button" wire:click="loadPatients" wire:loading.attr="disabled"
                                    class="btn btn-lg btn-outline-secondary">-- VIEW CURRENT DIAGNOSIS --
                            </button>
                        </center>
                    </div>
                @endif
            </div>
        @else
            <div class="row">
                <div class="col-md-12">
                    <br>
                    <br>
                    <div class="card">
                        <div class="card-header bg-success text-center text-uppercase"><b>DIAGNOSIS RESULTS</b>
                            <button type="button" wire:click="loadDiagnosis" wire:loading.attr="disabled"
                                    class="btn btn-link text-white float-right"><b>Reload</b>
                            </button>
                        </div>
                        <div class="card-body">
                            @include('inc.alert')
                            <div wire:poll.60000ms="loadDiagnosis">{{--Reloads after every 2 minutes--}}
                                @if(count($patientDiagnosis))
                                    <table class="table-responsive table table-hover col-md-12">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Issue ID</th>
                                            <th>Issue Name</th>
                                            <th>Issue Description</th>
                                            <th>Issue Accuracy</th>
                                            <th>Created</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php($count = 1)
                                        @foreach($patientDiagnosis as $patientDiagnose)
                                            <tr>
                                                <td>{{ $count++ }}</td>
                                                <td>{{ $patientDiagnose->issueID }}</td>
                                                <td>{{ $patientDiagnose->name }}</td>
                                                <td>{{ $patientDiagnose->issueDescription }}</td>
                                                <td>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-success bg-success"
                                                             role="progressbar"
                                                             aria-valuenow="40"
                                                             aria-valuemin="0" aria-valuemax="100"
                                                             style="width:{{ $patientDiagnose->accuracy }}%">
                                                            {{ number_format($patientDiagnose->accuracy,2) }}% Accuracy
                                                            (success)
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ \App\Http\Controllers\SystemController::elapsedTime($patientDiagnose->created_at) }}</td>
                                                <td>
                                                    @if($patientDiagnose->is_valid)
                                                        <button type="button" disabled
                                                                class="btn btn-success btn-sm">VALID
                                                        </button>
                                                    @else
                                                        <button type="button"
                                                                wire:click="markDiagnosisAsValid('{{ $patientDiagnose->id }}')"
                                                                wire:loading.attr="disabled"
                                                                class="btn btn-primary btn-sm">
                                                            MARK AS VALID
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <hr>
                                    <div class="form-group">
                                        <button wire:loading.attr="disabled"
                                                class="btn btn-outline-danger float-left"> CANCEL AND VIEW PATIENTS
                                        </button>
                                        <button wire:click="proceedToPatients" wire:loading.attr="disabled"
                                                class="btn btn-outline-success float-right">CONTINUE
                                        </button>
                                    </div>
                                @else
                                    <hr>
                                    <h4 class="text-center text-info"><b>No Diagnosis were found.</b></h4>
                                    <hr>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <div class="row">
        <div class="col-md-12">
            <br>
            <br>
            <div class="card-footer">
                <p class="text-center">&copy; {{ date('Y') }} {{ config('app.name') }}, powered By <span
                        class="text-danger">Api</span><b>Medic</b>. Designed By
                    <a href="https://v-ososi.site/" target="_blank">Vincent</a></p>
            </div>
        </div>
    </div>
</div>
