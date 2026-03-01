import React, { useState } from 'react'
import { loginUser } from '../services/storageService'

export default function Login({ onLoginSuccess, onGoToSignUp }) {
  const [email, setEmail] = useState('demo@example.com')
  const [senha, setSenha] = useState('123456')
  const [erro, setErro] = useState('')
  const [carregando, setCarregando] = useState(false)

  const handleSubmit = (e) => {
    e.preventDefault()
    setErro('')
    setCarregando(true)

    try {
      if (!email || !senha) {
        setErro('Informe e-mail e senha.')
        return
      }

      const user = loginUser(email, senha)
      onLoginSuccess(user)
    } catch (err) {
      setErro(err.message)
    } finally {
      setCarregando(false)
    }
  }

  return (
    <div id="login-page">
      <div id="side-content">
        <img src={`${import.meta.env.BASE_URL}criacao_de_login.jpeg`} alt="Federal Jato" />
      </div>
      <div id="form-container">
        <h3 className="title">Fazer Login</h3>

        {erro && <div className="alert erro">{erro}</div>}

        <form onSubmit={handleSubmit}>
          <input
            type="email"
            placeholder="Email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
          />
          <input
            type="password"
            placeholder="Senha"
            value={senha}
            onChange={(e) => setSenha(e.target.value)}
            required
          />

          <button type="submit" className="btn primario" disabled={carregando}>
            {carregando ? 'Entrando...' : 'Acessar'}
          </button>
        </form>

        <p className="signup-link">
          Não tem conta? <a onClick={!carregando ? onGoToSignUp : null}>Criar uma nova</a>
        </p>

        <div className="demo-info">
          <p><strong>Dados de Demo:</strong></p>
          <p>Email: demo@example.com</p>
          <p>Senha: 123456</p>
        </div>
      </div>
    </div>
  )
}
