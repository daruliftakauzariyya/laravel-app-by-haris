import React, { useState, useEffect } from 'react';
import { BrowserRouter as Router, Routes, Route, Link, Navigate, useLocation } from 'react-router-dom';
import { Home, ClipboardList, User as UserIcon, Bell } from 'lucide-react';

// Screens
import Login from './screens/Login';
import Dashboard from './screens/Dashboard';
import TaskList from './screens/TaskList';
import TaskDetails from './screens/TaskDetails';
import Profile from './screens/Profile';

function Navigation() {
  const location = useLocation();
  const hideNav = location.pathname.includes('/tasks/') || location.pathname === '/login';

  if (hideNav) return null;

  return (
    <nav className="bottom-nav">
      <Link to="/" className={`nav-item ${location.pathname === '/' ? 'active' : ''}`}>
        <Home />
        <span>Home</span>
      </Link>
      <Link to="/tasks" className={`nav-item ${location.pathname === '/tasks' ? 'active' : ''}`}>
        <ClipboardList />
        <span>Inquiries</span>
      </Link>
      <Link to="/profile" className={`nav-item ${location.pathname === '/profile' ? 'active' : ''}`}>
        <UserIcon />
        <span>Profile</span>
      </Link>
    </nav>
  );
}

function Header() {
  const location = useLocation();
  if (location.pathname === '/login') return null;

  return (
    <header style={{ position: 'fixed', top: 0, left: 0, right: 0, height: '64px', background: '#fff', display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: '0 16px', zIndex: 1000, borderBottom: '1px solid #f1f5f9' }}>
      <img src="https://new.daruliftakauzariyya.com/assets/logo/logoblueNew.svg" alt="IFTA" style={{ height: '32px' }} />
      <div style={{ position: 'relative' }}>
          <Bell size={24} color="var(--primary)" />
          <div style={{ position: 'absolute', top: '-2px', right: '-2px', width: '8px', height: '8px', background: '#ef4444', borderRadius: '50%', border: '2px solid #fff' }}></div>
      </div>
    </header>
  );
}

function App() {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const savedUser = localStorage.getItem('mufti_user');
    const token = localStorage.getItem('mufti_token');
    if (savedUser && token) {
      setUser(JSON.parse(savedUser));
    }
    setLoading(false);
  }, []);

  if (loading) return null;

  return (
    <Router>
      {!user ? (
        <Routes>
          <Route path="*" element={<Login onLogin={(u) => setUser(u)} />} />
        </Routes>
      ) : (
        <>
          <Header />
          <Routes>
            <Route path="/" element={<Dashboard />} />
            <Route path="/tasks" element={<TaskList />} />
            <Route path="/tasks/:id" element={<TaskDetails />} />
            <Route path="/profile" element={<Profile />} />
            <Route path="*" element={<Navigate to="/" />} />
          </Routes>
          <Navigation />
        </>
      )}
    </Router>
  );
}

export default App;
