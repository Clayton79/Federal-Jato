import React, { useState, useEffect } from 'react'
import './App.css'
import { getCurrentUser, initializeSampleData } from './services/storageService'
import Auth from './components/Auth'
import Dashboard from './components/Dashboard'

function App() {
  const [user, setUser] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    initializeSampleData()
    
    const currentUser = getCurrentUser()
    if (currentUser) {
      setUser(currentUser)
    }
    setLoading(false)
  }, [])

  const handleAuthSuccess = (userData) => {
    setUser(userData)
  }

  const handleLogout = () => {
    setUser(null)
  }

  if (loading) {
    return <div className="loading">Carregando...</div>
  }

  if (!user) {
    return <Auth onAuthSuccess={handleAuthSuccess} />
  }

  return <Dashboard user={user} onLogout={handleLogout} />
}

export default App
