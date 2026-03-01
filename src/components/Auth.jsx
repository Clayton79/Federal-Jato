import React, { useState, useEffect } from 'react'
import Login from './Login'
import SignUp from './SignUp'

const authStyles = `
@import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;600;800&display=swap');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html, body {
  height: 100%;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
}

#login-page {
  min-height: 100vh;
  display: grid;
  grid-template-columns: 1fr 1fr;
  background: white;
}

#side-content {
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #4c5fd5 0%, #3d4ec4 100%);
  overflow: hidden;
}

#side-content img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

#form-container {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: flex-start;
  padding: 48px 64px;
}

.title {
  font-size: 28px;
  font-weight: 800;
  color: #111827;
  margin-bottom: 16px;
}

form {
  width: 100%;
  max-width: 420px;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

input {
  width: 420px;
  max-width: 90%;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 12px 14px;
  font-size: 14px;
  margin-bottom: 10px;
  outline: 0;
  font-family: inherit;
  transition: all 0.2s;
}

input:focus {
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
  border-color: #c7d2fe;
}

.side-by-side {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  width: 100%;
}

.alert {
  background: #fee2e2;
  color: #991b1b;
  border-radius: 10px;
  padding: 10px 12px;
  margin-bottom: 12px;
  font-size: 0.95rem;
}

.alert.ok {
  background: #dcfce7;
  color: #166534;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  font-family: inherit;
}

.btn.primario {
  background: linear-gradient(135deg, #4c5fd5 0%, #3d4ec4 100%);
  color: white;
  width: 100%;
  justify-content: center;
}

.btn.primario:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(76, 95, 213, 0.3);
}

.signup-link {
  margin-top: 1rem;
  text-align: left;
  color: #6b7280;
  font-size: 0.95rem;
}

.signup-link a {
  color: #4c5fd5;
  cursor: pointer;
  font-weight: 600;
  text-decoration: none;
}

.signup-link a:hover {
  text-decoration: underline;
}

.demo-info {
  margin-top: 2rem;
  padding: 12px 16px;
  background: #f5f7fa;
  border-radius: 8px;
  font-size: 0.9rem;
  color: #374151;
  border-left: 3px solid #4c5fd5;
}

.demo-info p {
  margin: 4px 0;
  font-size: 0.85rem;
}

.date-section {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.date-section label {
  font-size: 0.85rem;
  color: #6b7280;
  font-weight: 600;
}

.birthday-input {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
}

.birthday-input input {
  width: 100%;
  max-width: none;
  margin-bottom: 0;
}

@media (max-width: 768px) {
  #login-page {
    grid-template-columns: 1fr;
  }

  #side-content {
    display: none;
  }

  #form-container {
    padding: 32px 24px;
  }

  input {
    width: 100%;
  }

  .side-by-side {
    grid-template-columns: 1fr;
  }
}
`

export default function Auth({ onAuthSuccess }) {
  const [showSignUp, setShowSignUp] = useState(false)

  useEffect(() => {
    const styleTag = document.createElement('style')
    styleTag.textContent = authStyles
    document.head.appendChild(styleTag)
    
    return () => styleTag.remove()
  }, [])

  return showSignUp ? (
    <SignUp 
      onSignUpSuccess={onAuthSuccess} 
      onGoToLogin={() => setShowSignUp(false)} 
    />
  ) : (
    <Login 
      onLoginSuccess={onAuthSuccess} 
      onGoToSignUp={() => setShowSignUp(true)} 
    />
  )
}

