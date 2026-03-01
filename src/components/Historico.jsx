import React, { useState, useEffect } from 'react'
import { getAllHistorico } from '../services/storageService'

export default function Historico() {
  const [historico, setHistorico] = useState([])
  const [page, setPage] = useState(1)

  const porPagina = 15

  useEffect(() => {
    const dados = getAllHistorico()
    setHistorico(dados.reverse())
  }, [])

  const paginadas = historico.slice((page - 1) * porPagina, page * porPagina)
  const totalPages = Math.ceil(historico.length / porPagina)

  const getIcon = (tipo) => {
    switch(tipo) {
      case 'comanda':
        return '📋'
      case 'despesa':
        return '💳'
      default:
        return '📝'
    }
  }

  const getColor = (tipo) => {
    switch(tipo) {
      case 'comanda':
        return '#3b82f6'
      case 'despesa':
        return '#ef4444'
      default:
        return '#6b7280'
    }
  }

  return (
    <div className="container">
      <div className="header-page">
        <h1>Histórico</h1>
        <p>Acompanhe todas as movimentações do sistema</p>
      </div>

      <div className="card">
        <div className="card-header">📜 Histórico de Ações</div>
        <div className="card-body">
          {historico.length === 0 ? (
            <div style={{ textAlign: 'center', color: '#9ca3af', padding: '40px 20px' }}>
              <p>Nenhuma ação registrada ainda.</p>
            </div>
          ) : (
            <>
              <div className="historico-list">
                {paginadas.map(h => (
                  <div key={h.id} className="historico-item">
                    <div className="historico-icon" style={{ color: getColor(h.tipo) }}>
                      {getIcon(h.tipo)}
                    </div>
                    <div className="historico-content">
                      <div className="historico-title">
                        <strong>{h.tipo.charAt(0).toUpperCase() + h.tipo.slice(1)}</strong>
                        <span className="historico-acao">{h.acao}</span>
                      </div>
                      <div className="historico-details">
                        <span>{h.usuario}</span>
                        <span className="historico-timestamp">
                          {new Date(h.data).toLocaleString('pt-BR')}
                        </span>
                      </div>
                    </div>
                  </div>
                ))}
              </div>

              {totalPages > 1 && (
                <div className="pagination">
                  <button 
                    className="btn neutro" 
                    onClick={() => setPage(Math.max(1, page - 1))}
                    disabled={page === 1}
                  >
                    ← Anterior
                  </button>
                  <span>{page} de {totalPages}</span>
                  <button 
                    className="btn neutro" 
                    onClick={() => setPage(Math.min(totalPages, page + 1))}
                    disabled={page === totalPages}
                  >
                    Próximo →
                  </button>
                </div>
              )}
            </>
          )}
        </div>
      </div>
    </div>
  )
}
