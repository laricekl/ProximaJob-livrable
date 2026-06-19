
<div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto rounded-[2rem] border border-outline-variant/20 bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-outline-variant/10 p-6">
            <h2 class="text-xl font-semibold text-primary">Modifier l'utilisateur</h2>
            <button onclick="closeEditModal()" class="flex h-10 w-10 items-center justify-center rounded-full text-outline transition-colors hover:bg-surface-container-low hover:text-primary">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <form id="editUserForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-6">
                <!-- Informations de base utilisateur -->
                <div class="border-b pb-4">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Informations personnelles</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
                            <input type="text" id="edit_name" name="name" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="edit_prenom" class="block text-sm font-medium text-gray-700 mb-2">Prénom *</label>
                            <input type="text" id="edit_prenom" name="prenom" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="edit_email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input type="email" id="edit_email" name="email" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="edit_telephone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                            <input type="tel" id="edit_telephone" name="telephone" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <label for="edit_adresse" class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                            <input type="text" id="edit_adresse" name="adresse" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="edit_role" class="block text-sm font-medium text-gray-700 mb-2">Rôle *</label>
                            <select id="edit_role" name="role" required onchange="toggleEntrepriseSection()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" @readonly(true)>
                                <option value="">Sélectionner un rôle</option>
                                <option value="admin">Administrateur</option>
                                <option value="entreprise">Entreprise</option>
                                <option value="candidat">Candidat</option>
                                <option value="Marketing">Responsable Marketing</option>

                            </select>
                        </div>
                        <div>
                            <label for="edit_profile_photo" class="block text-sm font-medium text-gray-700 mb-2">Photo de profil</label>
                            <input type="file" id="edit_profile_photo" name="profile_photo" accept="image/*"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <div id="current_photo_display" class="mt-2 hidden">
                                <img id="current_photo" src="" alt="Photo actuelle" class="w-16 h-16 rounded-full object-cover">
                                <p class="text-sm text-gray-500 mt-1">Photo actuelle</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section entreprise (cachée par défaut) -->
                <div id="entrepriseSection" class="border-b pb-4 hidden">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Informations entreprise</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="edit_company_name" class="block text-sm font-medium text-gray-700 mb-2">Nom de l'entreprise</label>
                            <input type="text" id="edit_company_name" name="company_name" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="edit_description" name="description" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div>
                            <label for="edit_website" class="block text-sm font-medium text-gray-700 mb-2">Site web</label>
                            <input type="url" id="edit_website" name="website" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="edit_" class="block text-sm font-medium text-gray-700 mb-2">neq</label>
                            <input type="text" id="edit_neq" name="neq" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="edit_logo" class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                            <input type="file" id="edit_logo" name="logo" accept="image/*"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <div id="current_logo_display" class="mt-2 hidden">
                                <img id="current_logo" src="" alt="Logo actuel" class="w-16 h-16 rounded object-cover">
                                <p class="text-sm text-gray-500 mt-1">Logo actuel</p>
                            </div>
                        </div>
                        
                    </div>
                </div>
<!--
              <div class="border-b pb-4">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Changer le mot de passe (optionnel)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_password" class="block text-sm font-medium text-gray-700 mb-2">Nouveau mot de passe</label>
                            <input type="password" id="edit_password" name="password" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="edit_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmer le mot de passe</label>
                            <input type="password" id="edit_password_confirmation" name="password_confirmation" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            -->
            </div>

            <div class="flex items-center justify-end space-x-4 border-t border-outline-variant/10 bg-surface-container-low/50 p-6">
                <button type="button" onclick="closeEditModal()" 
                    class="rounded-xl border border-outline-variant/20 px-4 py-2 text-sm font-semibold text-outline transition-colors hover:bg-surface-container-low">
                    Annuler
                </button>
                <button type="submit" 
                    class="rounded-xl bg-secondary-container px-4 py-2 text-sm font-bold text-white transition-colors hover:bg-secondary">
                    Modifier
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Variables globales
let currentUserId = null;

// Fonction pour ouvrir le modal d'édition
function openEditModal(userId) {
    currentUserId = userId;
    
    // Afficher le modal
    document.getElementById('editUserModal').classList.remove('hidden');
    
    // Charger les données de l'utilisateur
    loadUserData(userId);
}

// Fonction pour fermer le modal
function closeEditModal() {
    document.getElementById('editUserModal').classList.add('hidden');
    document.getElementById('editUserForm').reset();
    document.getElementById('entrepriseSection').classList.add('hidden');
    currentUserId = null;
}

// Fonction pour charger les données utilisateur
async function loadUserData(userId) {
    try {
        const response = await fetch(`/admin/users/${userId}/edit`);
        const data = await response.json();
        
        if (data.success) {
            const user = data.user;
            
            // Remplir les champs utilisateur
            document.getElementById('edit_name').value = user.name || '';
            document.getElementById('edit_prenom').value = user.prenom || '';
            document.getElementById('edit_email').value = user.email || '';
            document.getElementById('edit_telephone').value = user.telephone || '';
            document.getElementById('edit_adresse').value = user.adresse || '';
            
            // Sélectionner le rôle
            const roleSelect = document.getElementById('edit_role');
            const userRole = user.roles && user.roles.length > 0 ? user.roles[0].name : '';
            roleSelect.value = userRole;
            
            // Afficher la photo actuelle si elle existe
            if (user.profile_photo_path) {
                const photoDisplay = document.getElementById('current_photo_display');
                const photoImg = document.getElementById('current_photo');
                photoImg.src = `/assets/images/user_pdp/${user.profile_photo_path}`;
                photoDisplay.classList.remove('hidden');
            }
            
            // Si c'est une entreprise, remplir les champs entreprise
            if (userRole === 'entreprise' && user.entreprise) {
                const entreprise = user.entreprise;
                
                document.getElementById('edit_company_name').value = entreprise.company_name || '';
                document.getElementById('edit_description').value = entreprise.description || '';
                document.getElementById('edit_website').value = entreprise.website || '';
               // document.getElementById('edit_neq').value = entreprise.neq || '';
               // document.getElementById('edit_rccm').value = entreprise.rccm || '';
                
                // Afficher le logo actuel
                if (entreprise.logo) {
                    const logoDisplay = document.getElementById('current_logo_display');
                    const logoImg = document.getElementById('current_logo');
                    logoImg.src = `/assets/images/company_logos/${entreprise.logo}`;
                    logoDisplay.classList.remove('hidden');
                }
                
                // Afficher le lien vers l'extrait RCCM
                if (entreprise.extrait_rccm) {
                    const extraitDisplay = document.getElementById('current_extrait_display');
                    const extraitLink = document.getElementById('current_extrait_link');
                    extraitLink.href = `/assets/documents/rccm/${entreprise.extrait_rccm}`;
                    extraitDisplay.classList.remove('hidden');
                }
                
                // Afficher la section entreprise
                document.getElementById('entrepriseSection').classList.remove('hidden');
            }
            
            // Configurer l'action du formulaire
            document.getElementById('editUserForm').action = `/admin/users/${userId}`;
            
        } else {
            alert('Erreur lors du chargement des données utilisateur');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors du chargement des données utilisateur');
    }
}

// Fonction pour toggle la section entreprise
function toggleEntrepriseSection() {
    const roleSelect = document.getElementById('edit_role');
    const entrepriseSection = document.getElementById('entrepriseSection');
    
    if (roleSelect.value === 'entreprise') {
        entrepriseSection.classList.remove('hidden');
    } else {
        entrepriseSection.classList.add('hidden');
    }
}

// Gestionnaire de soumission du formulaire
  
document.getElementById('editUserForm').addEventListener('submit', function(e) {
    e.preventDefault(); // on bloque la soumission classique

    Swal.fire({
        title: 'Confirmation requise',
        text: 'Veuillez entrer votre mot de passe pour confirmer la modification',
        input: 'password',
        inputAttributes: {
            autocapitalize: 'off',
            placeholder: 'Mot de passe'
        },
        showCancelButton: true,
        confirmButtonText: 'Vérifier',
        cancelButtonText: 'Annuler',
        showLoaderOnConfirm: true,
        preConfirm: (password) => {
            if (!password) {
                Swal.showValidationMessage('Veuillez saisir votre mot de passe');
                return false;
            }
            return fetch('/admin/verify-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                return true;
            })
            .catch(error => {
                Swal.showValidationMessage(error.message);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            // Mot de passe correct, soumettre le formulaire normalement
            submitEditForm();
        }
    });
});

// Fonction pour soumettre le formulaire avec SweetAlert2 messages
function submitEditForm() {
    const form = document.getElementById('editUserForm');
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: 'Utilisateur modifié avec succès',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                closeEditModal();
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: data.message || 'Erreur inconnue'
            });
        }
    })
    .catch(() => {
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Une erreur est survenue'
        });
    });
}
 


// Fermer le modal en cliquant à l'extérieur
document.getElementById('editUserModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>
