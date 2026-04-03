<?php
require __DIR__ . '/../partials/left-panel.php';

$error = $error ?? '';
?>
<div class="dash-content-wrapper">
    <div class="container-fluid py-3 profile-wizard">
        <style>
            .profile-wizard {
                --pw-primary: #2563eb;
                --pw-border: #e5e7eb;
                --pw-muted: #6b7280;
                max-width: 920px;
            }
            .profile-wizard .wizard-title {
                font-size: 1.35rem;
                font-weight: 700;
                color: #111827;
            }
            .profile-wizard .wizard-sub {
                color: var(--pw-muted);
                font-size: 0.95rem;
            }
            .profile-wizard .step-nav {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 8px;
                margin: 1.25rem 0 1rem;
            }
            .profile-wizard .step-pill {
                text-align: center;
                padding: 10px 8px;
                border-radius: 8px;
                border: 1px solid var(--pw-border);
                background: #f9fafb;
                font-size: 0.8rem;
                font-weight: 600;
                color: #4b5563;
                cursor: pointer;
                transition: background 0.15s, border-color 0.15s, color 0.15s;
            }
            .profile-wizard .step-pill.active {
                background: var(--pw-primary);
                border-color: #1d4ed8;
                color: #fff;
            }
            .profile-wizard .step-pill.done:not(.active) {
                border-color: #86efac;
                background: #ecfdf5;
                color: #166534;
            }
            .profile-wizard .progress {
                height: 6px;
                border-radius: 99px;
                background: #e5e7eb;
            }
            .profile-wizard .progress-bar {
                border-radius: 99px;
                background: var(--pw-primary);
                transition: width 0.25s ease;
            }
            .profile-wizard .step-card {
                background: #fff;
                border: 1px solid var(--pw-border);
                border-radius: 12px;
                padding: 1.35rem 1.25rem;
                box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            }
            .profile-wizard .step-card .form-group { margin-bottom: 15px; }
            .profile-wizard .step-pane { display: none; }
            .profile-wizard .step-pane.active { display: block; }
            .profile-wizard label {
                font-size: 0.875rem;
                font-weight: 600;
                color: #374151;
                margin-bottom: 0.35rem;
            }
            .profile-wizard .req label::after { content: ' *'; color: #dc2626; }
            .profile-wizard .form-control {
                border-radius: 8px;
                border: 1px solid #d1d5db;
                font-size: 0.95rem;
            }
            .profile-wizard .form-control:focus {
                border-color: #22c55e;
                box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15);
            }
            .profile-wizard .wizard-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                justify-content: flex-end;
                margin-top: 1.25rem;
                padding-top: 1rem;
                border-top: 1px solid var(--pw-border);
            }
            .profile-wizard .btn-primary {
                background: var(--pw-primary);
                border: none;
                font-weight: 600;
                padding: 0.55rem 1.25rem;
                border-radius: 8px;
            }
            .profile-wizard .btn-default {
                border-radius: 8px;
                font-weight: 600;
            }
            @media (max-width: 700px) {
                .profile-wizard .step-nav { grid-template-columns: repeat(2, 1fr); }
            }
        </style>

        <p class="wizard-title mb-1">Complete your profile</p>
        <p class="wizard-sub mb-0">A few short steps — same details you’d add in your account, without staff-only fields.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mt-3"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form id="profile_complete_form"
              method="post"
              action="<?= BASE_URL ?>/dashboard/profile-complete/save"
              novalidate>

            <div class="step-nav" role="tablist">
                <button type="button" class="step-pill active" data-step="0" aria-current="step">Career</button>
                <button type="button" class="step-pill" data-step="1">Lifestyle</button>
                <button type="button" class="step-pill" data-step="2">Appearance</button>
                <button type="button" class="step-pill" data-step="3">Heritage &amp; height</button>
            </div>
            <div class="progress mb-3">
                <div class="progress-bar" id="wizard_progress" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <div class="step-card">
                <div class="step-pane active" data-pane="0">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group req">
                                <label for="education">Education</label>
                                <input class="form-control" id="education" name="education" required autocomplete="organization-title">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group req">
                                <label for="occupation">Occupation</label>
                                <input class="form-control" id="occupation" name="occupation" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="annual_income">Annual income</label>
                                <input class="form-control" id="annual_income" name="annual_income" placeholder="e.g. 500k – 1M">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="step-pane" data-pane="1">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="eating_habits">Eating habits</label>
                                <select class="form-control" id="eating_habits" name="eating_habits">
                                    <option value="">Select</option>
                                    <option>Vegetarian</option>
                                    <option>Non-Vegetarian</option>
                                    <option>Eggetarian</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="drinking">Drinking</label>
                                <select class="form-control" id="drinking" name="drinking">
                                    <option>No</option>
                                    <option>Occasionally</option>
                                    <option>Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="smoking">Smoking</label>
                                <select class="form-control" id="smoking" name="smoking">
                                    <option>No</option>
                                    <option>Occasionally</option>
                                    <option>Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="step-pane" data-pane="2">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="appearance">Appearance</label>
                                <input class="form-control" id="appearance" name="appearance">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="complexion">Complexion</label>
                                <select class="form-control" id="complexion" name="complexion">
                                    <option value="">Select</option>
                                    <option>Fair</option>
                                    <option>Wheatish</option>
                                    <option>Dark</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="body_type">Body type</label>
                                <select class="form-control" id="body_type" name="body_type">
                                    <option value="">Select</option>
                                    <option>Slim</option>
                                    <option>Average</option>
                                    <option>Athletic</option>
                                    <option>Heavy</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="step-pane" data-pane="3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="horoscope_details">Horoscope details</label>
                                <input class="form-control" id="horoscope_details" name="horoscope_details">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cast">Caste / community</label>
                                <input class="form-control" id="cast" name="cast">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="height">Height</label>
                                <input class="form-control" id="height" name="height" placeholder="e.g. 5'8&quot; or 173 cm">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mother_tongue">Mother tongue</label>
                                <input class="form-control" id="mother_tongue" name="mother_tongue">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wizard-actions">
                    <button type="button" class="btn btn-default" id="wizard_back" style="display:none;">Back</button>
                    <button type="button" class="btn btn-primary" id="wizard_next">Next</button>
                    <button type="submit" class="btn btn-primary" id="wizard_submit" style="display:none;">Save profile</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    var total = 4;
    var step = 0;
    var form = document.getElementById('profile_complete_form');
    var pills = document.querySelectorAll('.profile-wizard .step-pill');
    var panes = document.querySelectorAll('.profile-wizard .step-pane');
    var btnBack = document.getElementById('wizard_back');
    var btnNext = document.getElementById('wizard_next');
    var btnSubmit = document.getElementById('wizard_submit');
    var bar = document.getElementById('wizard_progress');

    function syncUI() {
        var pct = Math.round(((step + 1) / total) * 100);
        bar.style.width = pct + '%';
        bar.setAttribute('aria-valuenow', String(pct));

        pills.forEach(function (p, i) {
            p.classList.toggle('active', i === step);
            p.classList.toggle('done', i < step);
            p.setAttribute('aria-current', i === step ? 'step' : 'false');
        });
        panes.forEach(function (pane, i) {
            pane.classList.toggle('active', i === step);
        });

        btnBack.style.display = step === 0 ? 'none' : '';
        btnNext.style.display = step === total - 1 ? 'none' : '';
        btnSubmit.style.display = step === total - 1 ? '' : 'none';
    }

    function goTo(n) {
        if (n < 0 || n >= total) return;
        step = n;
        syncUI();
    }

    pills.forEach(function (pill) {
        pill.addEventListener('click', function () {
            goTo(parseInt(pill.getAttribute('data-step'), 10));
        });
    });

    btnBack.addEventListener('click', function () { goTo(step - 1); });

    btnNext.addEventListener('click', function () {
        if (step === 0) {
            var edu = form.querySelector('#education');
            var occ = form.querySelector('#occupation');
            if (!edu.value.trim() || !occ.value.trim()) {
                edu.reportValidity();
                occ.reportValidity();
                return;
            }
        }
        goTo(step + 1);
    });

    syncUI();
})();
</script>
