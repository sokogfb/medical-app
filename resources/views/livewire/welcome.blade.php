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
                                                <td>
                                                    <button type="button"
                                                            wire:click="removeSymptom('{{ $patientSymptom->id }}')"
                                                            wire:loading.attr="disabled" class="btn btn-danger btn-sm">
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
                        <button class="btn btn-lg btn-outline-secondary">-- VIEW CURRENT DIAGNOSIS --</button>
                    </center>
                </div>
            @endif
        </div>

    @else
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <br>
                <br>
                <div class="card">
                    <div class="card-header bg-success">DIAGNOSIS RESULTS</div>
                    <div class="card-body">
                        <p>testing 12</p>
                    </div>
                </div>
            </div>
            <div class="col-md-1"></div>
        </div>
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
