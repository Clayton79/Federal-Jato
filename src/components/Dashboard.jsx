import React, { useState, useEffect } from 'react'
import { logoutUser } from '../services/storageService'
import Navbar from './Navbar'
import DashboardHome from './DashboardHome'
import Comandas from './Comandas'
import Despesas from './Despesas'
import Historico from './Historico'

const dashboardStyles = `
.dashboard {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background-color: #f5f7fa;
}

.dashboard-content {
  flex: 1;
  padding: 0;
}

.header {
  background: linear-gradient(135deg, #4c5fd5 0%, #3d4ec4 100%);
  color: white;
  padding: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 4px 12px rgba(76, 95, 213, 0.2);
}

.header-title {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.header-title h1 {
  margin: 0;
  font-size: 2rem;
  font-weight: 700;
}

.header-title p {
  margin: 0;
  opacity: 0.95;
  font-size: 0.9rem;
  font-weight: 300;
}

.header .icon {
  width: 50px;
  height: 50px;
}

.user-info {
  font-size: 0.95rem;
}

.nav {
  background-color: #4c5fd5;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 2rem;
  gap: 2rem;
  box-shadow: 0 2px 8px rgba(76, 95, 213, 0.15);
}

.nav-items {
  display: flex;
  gap: 0;
}

.nav-item {
  background: none;
  border: none;
  color: white;
  padding: 1.2rem 1.8rem;
  display: flex;
  align-items: center;
  gap: 0.8rem;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 1rem;
  font-weight: 500;
  border-bottom: 3px solid transparent;
}

.nav-item:hover {
  background-color: rgba(255, 255, 255, 0.08);
}

.nav-item.active {
  border-bottom-color: #fe6201;
  background-color: rgba(255, 106, 1, 0.05);
}

.nav-item .icon {
  width: 24px;
  height: 24px;
}

.logout-btn {
  background-color: #fe6201;
  color: white;
  border: none;
  padding: 0.8rem 1.8rem;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
  font-family: inherit;
}

.logout-btn:hover {
  background-color: #e55a00;
  transform: translateY(-1px);
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
  width: 100%;
}

.header-page {
  margin-bottom: 2rem;
}

.header-page h1 {
  margin: 0 0 0.5rem 0;
  color: #2d3748;
  font-size: 1.8rem;
  font-weight: 700;
}

.header-page p {
  margin: 0;
  color: #718096;
  font-size: 0.95rem;
}

.alert {
  padding: 14px 18px;
  border-radius: 4px;
  margin-bottom: 1.5rem;
  font-size: 0.95rem;
  animation: slideIn 0.3s ease;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.alert.erro {
  background-color: #fee2e2;
  color: #991b1b;
  border-left: 4px solid #dc2626;
}

.alert.ok {
  background-color: #dcfce7;
  color: #166534;
  border-left: 4px solid #16a34a;
}

.card {
  background: white;
  border-radius: 4px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
  margin-bottom: 2rem;
  overflow: hidden;
  border: 1px solid #eaeef3;
}

.card-header {
  background-color: #f5f7fa;
  padding: 1.5rem;
  font-weight: 600;
  border-bottom: 1px solid #eaeef3;
  color: #2d3748;
  font-size: 1.1rem;
}

.card-body {
  padding: 2rem;
}

.cards-resumo {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.card-resumo {
  background: white;
  border-radius: 4px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
  border-left: 4px solid #4c5fd5;
  border: 1px solid #eaeef3;
}

.resumo-label {
  font-size: 0.85rem;
  color: #718096;
  margin-bottom: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.resumo-valor {
  font-size: 2.2rem;
  font-weight: 700;
  color: #2d3748;
}

.resumo-desc {
  font-size: 0.8rem;
  color: #a0aec0;
  margin-top: 0.5rem;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1.5rem;
}

.input-box {
  display: flex;
  flex-direction: column;
}

.input-box label {
  font-size: 0.9rem;
  font-weight: 500;
  margin-bottom: 0.5rem;
  color: #4a5568;
}

.input-clean,
.select-clean {
  padding: 11px 14px;
  border: 1px solid #cbd5e0;
  border-radius: 4px;
  font-size: 0.95rem;
  font-family: inherit;
  outline: none;
  transition: all 0.2s;
  background-color: white;
}

.input-clean:focus,
.select-clean:focus {
  border-color: #4c5fd5;
  box-shadow: 0 0 0 3px rgba(76, 95, 213, 0.1);
  background-color: #fafbff;
}

textarea.input-clean {
  resize: vertical;
  min-height: 100px;
}

.select-box {
  position: relative;
}

.select-caret {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
  color: #4c5fd5;
  font-size: 1.2rem;
}

.section-title {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 2.5rem 0 1.5rem 0;
  gap: 1rem;
  flex-wrap: wrap;
}

.section-title h2 {
  margin: 0;
  color: #2d3748;
  font-size: 1.5rem;
  font-weight: 600;
}

.section-title > div {
  display: flex;
  gap: 0.8rem;
}

.tabela-wrap {
  overflow-x: auto;
}

.tabela {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.95rem;
  background: white;
  border: 1px solid #eaeef3;
  border-radius: 4px;
  overflow: hidden;
}

.tabela thead {
  background-color: #f5f7fa;
}

.tabela th {
  padding: 13px 16px;
  text-align: left;
  font-weight: 600;
  color: #4a5568;
  border-bottom: 1px solid #e2e8f0;
}

.tabela td {
  padding: 13px 16px;
  border-bottom: 1px solid #f0f3f8;
  color: #2d3748;
}

.tabela tbody tr:hover {
  background-color: #f9fafb;
}

.badge {
  display: inline-block;
  padding: 5px 12px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
}

.badge.andamento {
  background-color: #fef3c7;
  color: #92400e;
}

.badge.finalizada {
  background-color: #dcfce7;
  color: #166534;
}

.acoes {
  display: flex;
  gap: 10px;
  align-items: center;
}

.acao {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  transition: transform 0.2s, color 0.2s;
  padding: 4px 8px;
  color: #4c5fd5;
}

.acao:hover {
  transform: scale(1.15);
  color: #3d4ec4;
}

.acao.delete {
  color: #e53e3e;
}

.acao.delete:hover {
  color: #c53030;
}

.btn {
  padding: 11px 18px;
  border: none;
  border-radius: 4px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  font-family: inherit;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.6rem;
  white-space: nowrap;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn.primario {
  background: linear-gradient(135deg, #4c5fd5 0%, #3d4ec4 100%);
  color: white;
  box-shadow: 0 2px 8px rgba(76, 95, 213, 0.2);
}

.btn.primario:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(76, 95, 213, 0.3);
}

.btn.neutro {
  background-color: #e2e8f0;
  color: #4a5568;
}

.btn.neutro:hover:not(:disabled) {
  background-color: #cbd5e0;
}

.btn.danger {
  background-color: #fc8181;
  color: white;
}

.btn.danger:hover:not(:disabled) {
  background-color: #f56565;
}

.paginacao,
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid #e2e8f0;
}

.paginacao span,
.pagination span {
  color: #718096;
  font-size: 0.9rem;
  margin: 0 1rem;
}

.pag-btn {
  padding: 8px 12px;
  border: 1px solid #cbd5e0;
  background-color: white;
  color: #4a5568;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  transition: all 0.2s;
}

.pag-btn:hover:not(.desabilitado) {
  border-color: #4c5fd5;
  color: #4c5fd5;
  background-color: #f9fafb;
}

.pag-btn.ativo {
  background-color: #4c5fd5;
  color: white;
  border-color: #4c5fd5;
}

.pag-btn.desabilitado {
  opacity: 0.5;
  cursor: not-allowed;
}

.pag-info {
  color: #718096;
  font-size: 0.9rem;
  margin: 0 1rem;
}

.historico-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.historico-item {
  display: flex;
  gap: 1.5rem;
  padding: 1.5rem;
  background: white;
  border: 1px solid #eaeef3;
  border-radius: 4px;
  align-items: flex-start;
  transition: all 0.2s;
}

.historico-item:hover {
  box-shadow: 0 2px 8px rgba(76, 95, 213, 0.1);
  border-color: #d0d9e8;
}

.historico-icon {
  font-size: 1.5rem;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  background-color: #f5f7fa;
  border-radius: 4px;
  color: #4c5fd5;
}

.historico-content {
  flex: 1;
  min-width: 0;
}

.historico-title {
  margin: 0 0 0.8rem 0;
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  align-items: center;
  font-weight: 600;
  color: #2d3748;
}

.historico-acao {
  color: #4c5fd5;
  font-weight: 600;
}

.historico-details {
  display: flex;
  gap: 1.5rem;
  flex-wrap: wrap;
  font-size: 0.85rem;
  color: #718096;
}

.historico-timestamp {
  color: #a0aec0;
  font-size: 0.8rem;
}

@media (max-width: 768px) {
  .header {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }

  .header-title {
    flex-direction: column;
    align-items: center;
  }

  .nav {
    flex-direction: column;
    padding: 0.5rem;
    gap: 0.5rem;
  }

  .nav-items {
    width: 100%;
    flex-wrap: wrap;
    justify-content: center;
  }

  .nav-item {
    padding: 0.9rem 1rem;
    font-size: 0.9rem;
  }

  .logout-btn {
    width: 100%;
  }

  .container {
    padding: 1.5rem;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .section-title {
    flex-direction: column;
    align-items: flex-start;
  }

  .tabela {
    font-size: 0.85rem;
  }

  .tabela th,
  .tabela td {
    padding: 10px;
  }

  .cards-resumo {
    grid-template-columns: 1fr;
  }

  .historico-item {
    gap: 1rem;
    padding: 1rem;
  }

  .historico-details {
    gap: 1rem;
  }
}

@media (max-width: 480px) {
  .header {
    padding: 1.5rem;
  }

  .header h1 {
    font-size: 1.3rem;
  }

  .header-page h1 {
    font-size: 1.3rem;
  }

  .nav-item {
    padding: 0.7rem 0.8rem;
    font-size: 0.8rem;
  }

  .nav-item .icon {
    width: 20px;
    height: 20px;
  }

  .btn {
    padding: 9px 14px;
    font-size: 0.85rem;
  }

  .acoes {
    gap: 6px;
  }

  .acao {
    font-size: 1rem;
  }

  .paginacao {
    gap: 0.3rem;
    font-size: 0.8rem;
  }

  .pag-btn {
    padding: 6px 10px;
  }
}
`

export default function Dashboard({ user, onLogout }) {
  const [currentPage, setCurrentPage] = useState('home')

  useEffect(() => {
    const styleTag = document.createElement('style')
    styleTag.textContent = dashboardStyles
    document.head.appendChild(styleTag)
    
    return () => styleTag.remove()
  }, [])

  const handleLogout = () => {
    logoutUser()
    onLogout()
  }

  const renderPage = () => {
    switch (currentPage) {
      case 'home':
        return <DashboardHome />
      case 'comandas':
        return <Comandas />
      case 'despesas':
        return <Despesas />
      case 'historico':
        return <Historico />
      default:
        return <DashboardHome />
    }
  }

  return (
    <div className="dashboard">
      <Navbar 
        user={user} 
        currentPage={currentPage} 
        onPageChange={setCurrentPage}
        onLogout={handleLogout}
      />
      <main className="dashboard-content">
        {renderPage()}
      </main>
    </div>
  )
}
