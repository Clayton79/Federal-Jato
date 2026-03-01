import React, { useState } from 'react'
import { createUser, loginUser } from '../services/storageService'

export default function SignUp({ onSignUpSuccess, onGoToLogin }) {
  const [nome, setNome] = useState('')
  const [sobrenome, setSobrenome] = useState('')
  const [email, setEmail] = useState('')
  const [celular, setCelular] = useState('')
  const [senha, setSenha] = useState('')
  const [dia, setDia] = useState('')
  const [mes, setMes] = useState('')
  const [ano, setAno] = useState('')
  const [erro, setErro] = useState('')
  const [ok, setOk] = useState('')
  const [carregando, setCarregando] = useState(false)

  const handleSubmit = (e) => {
    e.preventDefault()
    setErro('')
    setOk('')
    setCarregando(true)

    try {
      if (!nome || !sobrenome || !email || !senha) {
        setErro('Preencha nome, sobrenome, e-mail e senha.')
        return
      }

      let data_nascimento = null
      if (dia && mes && ano) {
        const diaStr = String(dia).padStart(2, '0')
        const mesStr = String(mes).padStart(2, '0')
        const anoStr = String(ano).padStart(4, '0')
        data_nascimento = `${anoStr}-${mesStr}-${diaStr}`
      }

      createUser({
        nome,
        sobrenome,
        email,
        celular,
        senha,
        data_nascimento
      })

      setOk('Conta criada! Fazendo login...')
      
      setTimeout(() => {
        const user = loginUser(email, senha)
        onSignUpSuccess(user)
      }, 1000)
    } catch (err) {
      setErro(err.message)
    } finally {
      setCarregando(false)
    }
  }

  return (
    <div id="login-page">
      <div id="side-content">
        <img src="/criacao_de_login.jpeg" alt="Federal Jato" />
      </div>
      <div id="form-container">
        <h3 className="title">Nova Conta</h3>

        {erro && <div className="alert erro">{erro}</div>}
        {ok && <div className="alert ok">{ok}</div>}

        <form onSubmit={handleSubmit}>
          <div className="side-by-side">
            <input
              type="text"
              placeholder="Nome"
              value={nome}
              onChange={(e) => setNome(e.target.value)}
              required
            />
            <input
              type="text"
              placeholder="Sobrenome"
              value={sobrenome}
              onChange={(e) => setSobrenome(e.target.value)}
              required
            />
          </div>
          <input
            type="email"
            placeholder="Email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
          />
          <input
            type="text"
            placeholder="Celular (opcional)"
            value={celular}
            onChange={(e) => setCelular(e.target.value)}
          />
          <input
            type="password"
            placeholder="Senha"
            value={senha}
            onChange={(e) => setSenha(e.target.value)}
            required
          />

          <div className="date-section">
            <label>Data de Nascimento (opcional)</label>
            <div className="birthday-input">
              <input
                type="text"
                placeholder="Dia"
                value={dia}
                onChange={(e) => setDia(e.target.value.slice(0, 2))}
                maxLength="2"
              />
              <input
                type="text"
                placeholder="Mês"
                value={mes}
                onChange={(e) => setMes(e.target.value.slice(0, 2))}
                maxLength="2"
              />
              <input
                type="text"
                placeholder="Ano"
                value={ano}
                onChange={(e) => setAno(e.target.value.slice(0, 4))}
                maxLength="4"
              />
            </div>
          </div>

          <button type="submit" className="btn primario" disabled={carregando}>
            {carregando ? 'Criando...' : 'Salvar'}
          </button>
        </form>

        <p className="signup-link">
          Já tem conta? <a onClick={!carregando ? onGoToLogin : null}>Fazer login</a>
        </p>
      </div>
    </div>
  )
}
