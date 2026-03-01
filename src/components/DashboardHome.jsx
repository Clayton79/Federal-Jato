import React, { useState, useEffect } from 'react'
import { getAllComandas, getAllDespesas, getAllHistorico, getCurrentUser } from '../services/storageService'

export default function DashboardHome() {
  const [stats, setStats] = useState({
    comandasAndamento: 0,
    comandasFinalizadas: 0,
    totalDespesas: 0,
    historicoRecente: []
  })

  useEffect(() => {
    const comandas = getAllComandas()
    const despesas = getAllDespesas()
    const historico = getAllHistorico()

    const andamento = comandas.filter(c => c.situacao === 'andamento').length
    const finalizadas = comandas.filter(c => c.situacao === 'finalizada').length
    const totalDespesas = despesas.reduce((sum, d) => sum + d.valor, 0)

    setStats({
      comandasAndamento: andamento,
      comandasFinalizadas: finalizadas,
      totalDespesas: totalDespesas,
      historicoRecente: historico.slice(-5).reverse()
    })
  }, [])

  const formatarMoeda = (valor) => {
    return new Intl.NumberFormat('pt-BR', {
      style: 'currency',
      currency: 'BRL'
    }).format(valor)
  }

  const formatarData = (data) => {
    return new Date(data).toLocaleDateString('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  }

  const getIconoTipo = (tipo) => {
    switch (tipo) {
      case 'comanda':
        return '📋'
      case 'despesa':
        return '💰'
      default:
        return '📝'
    }
  }

  return (
    <div className="container">
      <div className="header-page">
        <h1>Dashboard</h1>
        <p>Bem-vindo ao seu painel de controle</p>
      </div>

      <div className="cards-resumo">
        <div className="card-resumo" style={{ borderLeftColor: '#4c5fd5' }}>
          <div className="resumo-label">Comandas em Andamento</div>
          <div className="resumo-valor">{stats.comandasAndamento}</div>
          <div className="resumo-desc">Serviços em progresso</div>
        </div>

        <div className="card-resumo" style={{ borderLeftColor: '#10b981' }}>
          <div className="resumo-label">Comandas Finalizadas</div>
          <div className="resumo-valor">{stats.comandasFinalizadas}</div>
          <div className="resumo-desc">Serviços concluídos</div>
        </div>

        <div className="card-resumo" style={{ borderLeftColor: '#f59e0b' }}>
          <div className="resumo-label">Total de Despesas</div>
          <div className="resumo-valor" style={{ fontSize: '1.8rem' }}>
            {formatarMoeda(stats.totalDespesas)}
          </div>
          <div className="resumo-desc">Este mês</div>
        </div>
      </div>

      <div className="card">
        <div className="card-header">
          <div style={{ display: 'flex', alignItems: 'center', gap: '0.8rem' }}>
            <span style={{ fontSize: '1.3rem' }}>📊</span>
            <span>Atividade Recente</span>
          </div>
        </div>
        <div className="card-body">
          {stats.historicoRecente.length > 0 ? (
            <div className="historico-list">
              {stats.historicoRecente.map((item) => (
                <div key={item.id} className="historico-item">
                  <div className="historico-icon">
                    {getIconoTipo(item.tipo)}
                  </div>
                  <div className="historico-content">
                    <div className="historico-title">
                      <span className="historico-acao">
                        {item.tipo === 'comanda' ? 'Comanda' : 'Despesa'}
                      </span>
                      <span>{item.acao}</span>
                    </div>
                    <div className="historico-details">
                      <span>👤 {item.usuario}</span>
                      <span className="historico-timestamp">
                        {formatarData(item.data)}
                      </span>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div style={{ 
              textAlign: 'center', 
              padding: '2rem', 
              color: '#a0aec0'
            }}>
              <p style={{ fontSize: '1.1rem', margin: '0.5rem 0' }}>📭</p>
              <p>Nenhuma atividade registrada ainda</p>
            </div>
          )}
        </div>
      </div>

      <div className="quick-actions" style={{ marginTop: '2rem' }}>
        <div className="card">
          <div className="card-header">
            <div style={{ display: 'flex', alignItems: 'center', gap: '0.8rem' }}>
              <span style={{ fontSize: '1.3rem' }}>⚡</span>
              <span>Atalhos Rápidos</span>
            </div>
          </div>
          <div className="card-body" style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '1rem' }}>
            <div style={{
              padding: '1.5rem',
              borderRadius: '8px',
              backgroundColor: '#f3f4f6',
              textAlign: 'center',
              cursor: 'pointer',
              transition: 'all 0.2s',
              border: '1px solid #e5e7eb'
            }}
            onMouseEnter={(e) => {
              e.currentTarget.style.backgroundColor = '#e5e7eb'
              e.currentTarget.style.transform = 'translateY(-2px)'
              e.currentTarget.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)'
            }}
            onMouseLeave={(e) => {
              e.currentTarget.style.backgroundColor = '#f3f4f6'
              e.currentTarget.style.transform = 'translateY(0)'
              e.currentTarget.style.boxShadow = 'none'
            }}>
              <div style={{ fontSize: '2rem', marginBottom: '0.5rem' }}>📋</div>
              <div style={{ fontSize: '0.95rem', fontWeight: '600', color: '#1f2937' }}>
                Nova Comanda
              </div>
            </div>

            <div style={{
              padding: '1.5rem',
              borderRadius: '8px',
              backgroundColor: '#f3f4f6',
              textAlign: 'center',
              cursor: 'pointer',
              transition: 'all 0.2s',
              border: '1px solid #e5e7eb'
            }}
            onMouseEnter={(e) => {
              e.currentTarget.style.backgroundColor = '#e5e7eb'
              e.currentTarget.style.transform = 'translateY(-2px)'
              e.currentTarget.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)'
            }}
            onMouseLeave={(e) => {
              e.currentTarget.style.backgroundColor = '#f3f4f6'
              e.currentTarget.style.transform = 'translateY(0)'
              e.currentTarget.style.boxShadow = 'none'
            }}>
              <div style={{ fontSize: '2rem', marginBottom: '0.5rem' }}>💰</div>
              <div style={{ fontSize: '0.95rem', fontWeight: '600', color: '#1f2937' }}>
                Registrar Despesa
              </div>
            </div>

            <div style={{
              padding: '1.5rem',
              borderRadius: '8px',
              backgroundColor: '#f3f4f6',
              textAlign: 'center',
              cursor: 'pointer',
              transition: 'all 0.2s',
              border: '1px solid #e5e7eb'
            }}
            onMouseEnter={(e) => {
              e.currentTarget.style.backgroundColor = '#e5e7eb'
              e.currentTarget.style.transform = 'translateY(-2px)'
              e.currentTarget.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)'
            }}
            onMouseLeave={(e) => {
              e.currentTarget.style.backgroundColor = '#f3f4f6'
              e.currentTarget.style.transform = 'translateY(0)'
              e.currentTarget.style.boxShadow = 'none'
            }}>
              <div style={{ fontSize: '2rem', marginBottom: '0.5rem' }}>📊</div>
              <div style={{ fontSize: '0.95rem', fontWeight: '600', color: '#1f2937' }}>
                Ver Histórico
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}
