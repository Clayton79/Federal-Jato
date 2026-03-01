import React from 'react'

export default function Navbar({ user, currentPage, onPageChange, onLogout }) {
  return (
    <>
      <div className="header">
        <div className="header-title">
          <svg className="icon" fill="white" viewBox="0 0 24 24">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="white" fill="none" strokeWidth="2" />
            <line x1="16" y1="2" x2="16" y2="6" stroke="white" strokeWidth="2" />
            <line x1="8" y1="2" x2="8" y2="6" stroke="white" strokeWidth="2" />
            <line x1="3" y1="10" x2="21" y2="10" stroke="white" strokeWidth="2" />
          </svg>
          <div>
            <h1>Federal Jato</h1>
            <p>Sistema de Gestão de Lava-Jato</p>
          </div>
        </div>
        <div className="user-info">
          <span>👤 {user.nome}</span>
        </div>
      </div>

      <nav className="nav">
        <div className="nav-items">
          <button 
            className={`nav-item ${currentPage === 'home' ? 'active' : ''}`}
            onClick={() => onPageChange('home')}
          >
            <svg className="icon" fill="white" viewBox="0 0 24 24">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke="white" fill="none" strokeWidth="2" />
              <polyline points="9 22 9 12 15 12 15 22" stroke="white" fill="none" strokeWidth="2" />
            </svg>
            Dashboard
          </button>

          <button 
            className={`nav-item ${currentPage === 'comandas' ? 'active' : ''}`}
            onClick={() => onPageChange('comandas')}
          >
            <svg className="icon" fill="white" viewBox="0 0 24 24">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="white" fill="none" strokeWidth="2" />
              <line x1="16" y1="2" x2="16" y2="6" stroke="white" strokeWidth="2" />
              <line x1="8" y1="2" x2="8" y2="6" stroke="white" strokeWidth="2" />
              <line x1="3" y1="10" x2="21" y2="10" stroke="white" strokeWidth="2" />
            </svg>
            Comandas
          </button>

          <button 
            className={`nav-item ${currentPage === 'despesas' ? 'active' : ''}`}
            onClick={() => onPageChange('despesas')}
          >
            <svg className="icon" fill="white" viewBox="0 0 24 24">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="white" fill="none" strokeWidth="2" />
              <circle cx="12" cy="7" r="4" stroke="white" fill="none" strokeWidth="2" />
            </svg>
            Despesas
          </button>

          <button 
            className={`nav-item ${currentPage === 'historico' ? 'active' : ''}`}
            onClick={() => onPageChange('historico')}
          >
            <svg className="icon" fill="white" viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="10" stroke="white" fill="none" strokeWidth="2"/>
              <polyline points="12 6 12 12 16 14" stroke="white" fill="none" strokeWidth="2"/>
            </svg>
            Histórico
          </button>
        </div>

        <button className="logout-btn" onClick={onLogout}>
          Deslogar
        </button>
      </nav>
    </>
  )
}
