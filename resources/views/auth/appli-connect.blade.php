<style>
  /* Modal Styles */
    .connection-modal {
      display: none;
      position: fixed;
      z-index: 2000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(8px);
      justify-content: center;
      align-items: center;
      animation: modalFadeIn 0.3s ease;
    }
    
    @keyframes modalFadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    
    .connect-modal-content{
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      padding: 40px;
      border-radius: 24px;
      width: 90%;
      max-width: 460px;
      text-align: center;
      box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.2);
      position: relative;
      animation: modalSlideIn 0.3s ease;
    }
    
    @keyframes modalSlideIn {
      from { transform: translateY(-50px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
    
    .close {
      position: absolute;
      top: 20px;
      right: 24px;
      font-size: 28px;
      cursor: pointer;
      color: #94a3b8;
      transition: all 0.3s ease;
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
    }
    
    .close:hover {
      color: #ef4444;
      background: rgba(239, 68, 68, 0.1);
    }
    
    .modal h2 {
      font-size: 24px;
      font-weight: 700;
      background: linear-gradient(135deg, #667eea, #764ba2);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 32px;
    }
    
    .connect-form-group {
      margin-bottom: 24px;
      text-align: left;
    }
    
    .connect-form-group label {
      display: block;
      margin-bottom: 8px;
      color: #374151;
      font-weight: 500;
      font-size: 14px;
    }
    
    .connect-form-group input {
      width: 100%;
      padding: 16px;
      border: 2px solid #e5e7eb;
      border-radius: 12px;
      font-size: 16px;
      transition: all 0.3s ease;
      background: rgba(255, 255, 255, 0.8);
    }
    
    .connect-form-group input:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
      background: rgba(255, 255, 255, 1);
    }
    
    .forgot-password {
      text-align: right;
      margin-bottom: 24px;
    }
    
    .forgot-password a {
      color: #667eea;
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
    }
    
    .forgot-password a:hover {
      color: #764ba2;
    }
    

    .login-buttons {
      display: flex;
      flex-direction: column;
      gap: 16px;
      margin-bottom: 32px;
    }
    
    .login-btn {
      width: 100%;
      padding: 16px 20px;
      border: none;
      cursor: pointer;
      border-radius: 12px;
      font-weight: 600;
      font-size: 16px;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      position: relative;
      overflow: hidden;
    }
    
    .login-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s;
    }
    
    .login-btn:hover::before {
      left: 100%;
    }
    
    .login-btn.email {
      background: #007bff;
      color: white;
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }
    
    .login-btn.email:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 24px rgba(102, 126, 234, 0.4);
    }
    
    /* Icons */
    .icon {
      width: 20px;
      height: 20px;
      display: inline-block;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .container {
        flex-direction: column;
      }
      
      .left {
        flex: none;
        height: 200px;
      }
      
      .left img {
        width: 60%;
        max-width: 200px;
      }
      
      .login-box {
        padding: 24px;
        margin: 20px;
      }
      
      .welcome-text h1 {
        font-size: 24px;
      }
      
      .connect-modal-content {
        padding: 24px;
        margin: 20px;
      }
    }
</style>

<div id="jobseeker-modal" class="connection-modal">
  <div class="connect-modal-content">
    <span class="close" id="close-modal">&times;</span>
    <h2>Connexion Chercheur d'emploi</h2>

   
    <p id="connect-modal-message" class="text-muted" style="margin-bottom:1rem;display:none;"></p>
    
    <form method="POST" action="{{ route('appli.login') }}">
      @csrf
      <div class="connect-form-group">
        <label for="login-email">Adresse e-mail</label>
        <input type="email"
               id="login-email"
               name="email"
               class="connect-form-control"
               placeholder="votre@email.com"
               required>
      </div>
      
      <div class="connect-form-group">
        <label for="login-password">Mot de passe</label>
        <input type="password"
               id="login-password"
               name="password"
               required
               placeholder="••••••••">
      </div>

        
      
      <div class="forgot-password">
        <a href="{{ route('password.request') }}" target="_blank">Mot de passe oublié ?</a>
      </div>
 
      <div class="signup-link">
          <p>Vous n'avez pas de compte ? <a href="{{ route('register') }}">S'inscrire</a></p>
        </div>
      <button type="submit" class="login-btn email">
        <svg class="icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M9 16.17L4.83 12L3.41 13.41L9 19L21 7L19.59 5.59L9 16.17Z"/>
        </svg>
        Se connecter
      </button>
    </form>
  </div>
</div>
