 
<!-- Modal d'ajout d'utilisateur -->
<div id="addUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto rounded-[2rem] border border-outline-variant/20 bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-outline-variant/10 p-6">
            <h2 class="text-xl font-semibold text-primary">Ajouter un nouvel utilisateur</h2>
            <button onclick="closeAddModal()" class="flex h-10 w-10 items-center justify-center rounded-full text-outline transition-colors hover:bg-surface-container-low hover:text-primary">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <form id="addUserForm" method="POST" action="/admin/users" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="csrf-token-here">
            
            <div class="p-6 space-y-6">
                <!-- Informations de base utilisateur -->
                <div class="border-b pb-4">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Informations personnelles</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="add_name" class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
                            <input type="text" id="add_name" name="name" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="add_prenom" class="block text-sm font-medium text-gray-700 mb-2">Prénom *</label>
                            <input type="text" id="add_prenom" name="prenom" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="add_email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input type="email" id="add_email" name="email" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="add_telephone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                            <input type="tel" id="add_telephone" name="telephone" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <label for="add_adresse" class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                            <input type="text" id="add_adresse" name="adresse" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="add_role" class="block text-sm font-medium text-gray-700 mb-2">Rôle *</label>
                            <select id="add_role" name="role" required onchange="toggleAddEntrepriseSection()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Sélectionner un rôle</option>
                                <option value="admin">Administrateur</option>
                                <option value="entreprise">Entreprise</option>
                                <option value="candidat">Candidat</option>
                                <option value="Marketing">Responsable Marketing</option>
                            </select>
                        </div>
                        <div>
                            <label for="add_profile_photo" class="block text-sm font-medium text-gray-700 mb-2">Photo de profil</label>
                            <input type="file" id="add_profile_photo" name="profile_photo" accept="image/*"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Mot de passe -->
                <div class="border-b pb-4">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Sécurité</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="add_password" class="block text-sm font-medium text-gray-700 mb-2">Mot de passe *</label>
                            <input type="password" id="add_password" name="password" required minlength="8"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Au moins 8 caractères</p>
                        </div>
                        <div>
                            <label for="add_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmer le mot de passe *</label>
                            <input type="password" id="add_password_confirmation" name="password_confirmation" required minlength="8"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Section entreprise (cachée par défaut) -->
                <div id="addEntrepriseSection" class="border-b pb-4 hidden">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Informations entreprise</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="add_company_name" class="block text-sm font-medium text-gray-700 mb-2">Nom de l'entreprise *</label>
                            <input type="text" id="add_company_name" name="company_name" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <label for="add_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="add_description" name="description" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div>
                            <label for="add_website" class="block text-sm font-medium text-gray-700 mb-2">Site web</label>
                            <input type="url" id="add_website" name="website" placeholder="https://exemple.com"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="add_neq" class="block text-sm font-medium text-gray-700 mb-2">neq *</label>
                            <input type="text" id="add_neq" name="neq" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                      <!--  <div>
                            <label for="add_rccm" class="block text-sm font-medium text-gray-700 mb-2">RCCM *</label>
                            <input type="text" id="add_rccm" name="rccm" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>-->
                        <div>
                            <label for="add_logo" class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                            <input type="file" id="add_logo" name="logo" accept="image/*"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                       <!-- <div>
                            <label for="add_extrait_rccm" class="block text-sm font-medium text-gray-700 mb-2">Extrait RCCM</label>
                            <input type="file" id="add_extrait_rccm" name="extrait_rccm" accept=".pdf,.doc,.docx"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Formats acceptés: PDF, DOC, DOCX</p>
                        </div>-->
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 border-t border-outline-variant/10 bg-surface-container-low/50 p-6">
                <button type="button" onclick="closeAddModal()" 
                    class="rounded-xl border border-outline-variant/20 px-4 py-2 text-sm font-semibold text-outline transition-colors hover:bg-surface-container-low">
                    Annuler
                </button>
                <button type="submit" 
                    class="inline-flex items-center gap-2 rounded-xl bg-secondary-container px-4 py-2 text-sm font-bold text-white transition-colors hover:bg-secondary">
                    <span class="material-symbols-outlined text-lg">add</span>Créer l'utilisateur
                </button>
            </div>
        </form>
    </div>
</div>


<script>
// Fonction pour ouvrir le modal d'ajout
function openAddModal() {
    document.getElementById('addUserModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

// Fonction pour fermer le modal d'ajout
function closeAddModal() {
    document.getElementById('addUserModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    
    // Réinitialiser le formulaire
    document.getElementById('addUserForm').reset();
    
    // Cacher la section entreprise
    document.getElementById('addEntrepriseSection').classList.add('hidden');
    
    // Enlever les classes d'erreur
    document.querySelectorAll('.border-red-500').forEach(el => {
        el.classList.remove('border-red-500');
        el.classList.add('border-gray-300');
    });
    
    // Supprimer les messages d'erreur
    document.querySelectorAll('.text-red-500').forEach(el => {
        if (el.classList.contains('error-message')) {
            el.remove();
        }
    });
}

// Fonction pour toggle la section entreprise lors de l'ajout
function toggleAddEntrepriseSection() {
    const roleSelect = document.getElementById('add_role');
    const entrepriseSection = document.getElementById('addEntrepriseSection');
    const companyNameInput = document.getElementById('add_company_name');
    const neqInput = document.getElementById('add_neq');
    
    if (roleSelect.value === 'entreprise') {
        entrepriseSection.classList.remove('hidden');
        // Rendre les champs obligatoires
        companyNameInput.required = true;
        neqInput.required = true;
    } else {
        entrepriseSection.classList.add('hidden');
        // Enlever l'obligation
        companyNameInput.required = false;
        neqInput.required = false;
    }
}

// Validation des mots de passe
function validatePasswords() {
    const password = document.getElementById('add_password').value;
    const passwordConfirm = document.getElementById('add_password_confirmation').value;
    const confirmInput = document.getElementById('add_password_confirmation');
    
    // Supprimer les anciens messages d'erreur
    const existingError = confirmInput.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    if (password && passwordConfirm && password !== passwordConfirm) {
        confirmInput.classList.add('border-red-500');
        confirmInput.classList.remove('border-gray-300');
        
        // Ajouter message d'erreur
        const errorMsg = document.createElement('p');
        errorMsg.className = 'text-red-500 text-xs mt-1 error-message';
        errorMsg.textContent = 'Les mots de passe ne correspondent pas';
        confirmInput.parentNode.appendChild(errorMsg);
        return false;
    } else {
        confirmInput.classList.remove('border-red-500');
        confirmInput.classList.add('border-gray-300');
        return true;
    }
}

// Écouter les changements sur les champs mot de passe
document.getElementById('add_password').addEventListener('input', validatePasswords);
document.getElementById('add_password_confirmation').addEventListener('input', validatePasswords);

// Gestionnaire de soumission du formulaire d'ajout
document.getElementById('addUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Valider les mots de passe
    if (!validatePasswords()) {
        Swal.fire({
            icon: 'error',
            title: 'Erreur de validation',
            text: 'Veuillez corriger les erreurs dans le formulaire'
        });
        return;
    }
    
    // Confirmer la création avec mot de passe admin
    Swal.fire({
        title: 'Confirmation requise',
        text: 'Veuillez entrer votre mot de passe administrateur pour confirmer la création',
        input: 'password',
        inputAttributes: {
            autocapitalize: 'off',
            placeholder: 'Mot de passe administrateur'
        },
        showCancelButton: true,
        confirmButtonText: 'Vérifier et créer',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#059669',
        showLoaderOnConfirm: true,
        preConfirm: (password) => {
            if (!password) {
                Swal.showValidationMessage('Veuillez saisir votre mot de passe');
                return false;
            }
            
            
            return new Promise((resolve) => {
                
                fetch('/admin/verify-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 'demo-token'
                    },
                    body: JSON.stringify({ password })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.valid) {
                        resolve(true);
                    } else {
                        Swal.showValidationMessage('Mot de passe incorrect');
                        resolve(false);
                    }
                })
                .catch(() => {
                    // Pour la démo, on simule une validation réussie
                    if (password === 'admin123') {
                        resolve(true);
                    } else {
                        Swal.showValidationMessage('Mot de passe incorrect');
                        resolve(false);
                    }
                });
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            // Mot de passe correct, soumettre le formulaire
            submitAddForm();
        }
    });
});

// Fonction pour soumettre le formulaire d'ajout
function submitAddForm() {
    const form = document.getElementById('addUserForm');
    const formData = new FormData(form);
    
    // Afficher un loader
    Swal.fire({
        title: 'Création en cours...',
        text: 'Veuillez patienter',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Simuler l'envoi du formulaire
    setTimeout(() => {
        // Simuler une réponse réussie
        const success = Math.random() > 0.2; // 80% de chance de succès
        
        if (success) {
            Swal.fire({
                icon: 'success',
                title: 'Utilisateur créé !',
                text: 'Le nouvel utilisateur a été créé avec succès.',
                timer: 2000,
                showConfirmButton: false,
                confirmButtonColor: '#059669'
            }).then(() => {
                closeAddModal();
                // Dans un vrai contexte, recharger la page ou mettre à jour la liste
                // window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur est survenue lors de la création de l\'utilisateur.',
                confirmButtonColor: '#dc2626'
            });
        }
    }, 2000);
    
    /* Version réelle pour l'intégration :
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Utilisateur créé !',
                text: data.message || 'Le nouvel utilisateur a été créé avec succès.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                closeAddModal();
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: data.message || 'Une erreur est survenue'
            });
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Une erreur est survenue lors de la création'
        });
    });
    */
}

// Fermer le modal en cliquant à l'extérieur
document.getElementById('addUserModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddModal();
    }
});

// Empêcher la fermeture du modal quand on clique à l'intérieur
document.querySelector('#addUserModal > div').addEventListener('click', function(e) {
    e.stopPropagation();
});

// Validation en temps réel pour l'email
document.getElementById('add_email').addEventListener('blur', function() {
    const email = this.value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email && !emailRegex.test(email)) {
        this.classList.add('border-red-500');
        this.classList.remove('border-gray-300');
        
        // Ajouter message d'erreur s'il n'existe pas
        const existingError = this.parentNode.querySelector('.error-message');
        if (!existingError) {
            const errorMsg = document.createElement('p');
            errorMsg.className = 'text-red-500 text-xs mt-1 error-message';
            errorMsg.textContent = 'Veuillez entrer un email valide';
            this.parentNode.appendChild(errorMsg);
        }
    } else {
        this.classList.remove('border-red-500');
        this.classList.add('border-gray-300');
        
        // Supprimer le message d'erreur
        const existingError = this.parentNode.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
    }
});

// Validation des mots de passe pour l'ajout
function validateAddPasswords() {
    const password = document.getElementById('add_password').value;
    const passwordConfirm = document.getElementById('add_password_confirmation').value;
    const confirmInput = document.getElementById('add_password_confirmation');
    
    // Supprimer les anciens messages d'erreur
    const existingError = confirmInput.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    if (password && passwordConfirm && password !== passwordConfirm) {
        confirmInput.classList.add('border-red-500');
        confirmInput.classList.remove('border-gray-300');
        
        // Ajouter message d'erreur
        const errorMsg = document.createElement('p');
        errorMsg.className = 'text-red-500 text-xs mt-1 error-message';
        errorMsg.textContent = 'Les mots de passe ne correspondent pas';
        confirmInput.parentNode.appendChild(errorMsg);
        return false;
    } else {
        confirmInput.classList.remove('border-red-500');
        confirmInput.classList.add('border-gray-300');
        return true;
    }
}

// Validation en temps réel pour l'email d'ajout
function validateAddEmail() {
    const emailInput = document.getElementById('add_email');
    const email = emailInput.value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    // Supprimer le message d'erreur existant
    const existingError = emailInput.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    if (email && !emailRegex.test(email)) {
        emailInput.classList.add('border-red-500');
        emailInput.classList.remove('border-gray-300');
        
        const errorMsg = document.createElement('p');
        errorMsg.className = 'text-red-500 text-xs mt-1 error-message';
        errorMsg.textContent = 'Veuillez entrer un email valide';
        emailInput.parentNode.appendChild(errorMsg);
        return false;
    } else {
        emailInput.classList.remove('border-red-500');
        emailInput.classList.add('border-gray-300');
        return true;
    }
}

// Gestionnaire de soumission du formulaire d'ajout
document.getElementById('addUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Valider les mots de passe
    if (!validateAddPasswords()) {
        Swal.fire({
            icon: 'error',
            title: 'Erreur de validation',
            text: 'Veuillez corriger les erreurs dans le formulaire'
        });
        return;
    }
    
    // Valider l'email
    if (!validateAddEmail()) {
        Swal.fire({
            icon: 'error',
            title: 'Erreur de validation',
            text: 'Veuillez corriger l\'adresse email'
        });
        return;
    }
    
    // Confirmer la création avec mot de passe admin
    Swal.fire({
        title: 'Confirmation requise',
        text: 'Veuillez entrer votre mot de passe administrateur pour confirmer la création',
        input: 'password',
        inputAttributes: {
            autocapitalize: 'off',
            placeholder: 'Mot de passe administrateur'
        },
        showCancelButton: true,
        confirmButtonText: 'Vérifier et créer',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#059669',
        showLoaderOnConfirm: true,
        preConfirm: (password) => {
            if (!password) {
                Swal.showValidationMessage('Veuillez saisir votre mot de passe');
                return false;
            }
            
            if (password.length < 6) {
                Swal.showValidationMessage('Le mot de passe doit contenir au moins 6 caractères');
                return false;
            }
            
            // Vérifier le mot de passe admin
            return fetch('/admin/verify-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ password })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur serveur');
                }
                return response.json();
            })
            .then(data => {
                if (!data.valid) {
                    throw new Error('Mot de passe incorrect');
                }
                return data;
            })
            .catch(error => {
                Swal.showValidationMessage(error.message);
                return false;
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            // Mot de passe correct, soumettre le formulaire
            submitAddForm();
        }
    });
});

// Fonction pour soumettre le formulaire d'ajout
function submitAddForm() {
    const form = document.getElementById('addUserForm');
    const formData = new FormData(form);
    
    // Afficher un loader
    Swal.fire({
        title: 'Création en cours...',
        text: 'Veuillez patienter',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Utilisateur créé !',
                text: data.message || 'Le nouvel utilisateur a été créé avec succès.',
                timer: 2000,
                showConfirmButton: false,
                confirmButtonColor: '#059669'
            }).then(() => {
                closeAddModal();
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: data.message || 'Une erreur est survenue lors de la création'
            });
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Une erreur est survenue lors de la création'
        });
    });
}

// Event listeners pour la validation en temps réel
document.addEventListener('DOMContentLoaded', function() {
    // Validation des mots de passe
    document.getElementById('add_password').addEventListener('input', validateAddPasswords);
    document.getElementById('add_password_confirmation').addEventListener('input', validateAddPasswords);
    
    // Validation de l'email
    document.getElementById('add_email').addEventListener('blur', validateAddEmail);
    
    // Fermer le modal en cliquant à l'extérieur
    document.getElementById('addUserModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddModal();
        }
    });
    
    // Empêcher la fermeture du modal quand on clique à l'intérieur
    document.querySelector('#addUserModal > div').addEventListener('click', function(e) {
        e.stopPropagation();
    });
});
</script>

</body>
 
