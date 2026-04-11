import React, { useState } from 'react';
import client from '../api/client';
import { ShieldCheck, LogIn } from 'lucide-react';

export default function Login({ onLogin }) {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const handleLogin = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError('');
    
    try {
      const response = await client.post('/auth/login', { email, password });
      if (response.data.token) {
        localStorage.setItem('mufti_token', response.data.token);
        localStorage.setItem('mufti_user', JSON.stringify(response.data.user));
        onLogin(response.data.user);
      }
    } catch (err) {
      setError(err.response?.data?.message || 'Login failed. Please check your credentials.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="mobile-container" style={{ display: 'flex', flexDirection: 'column', justifyContent: 'center', paddingBottom: '40px' }}>
      <div style={{ textAlign: 'center', marginBottom: '40px' }}>
        <div style={{ 
          background: 'var(--primary)', 
          width: '80px', height: '80px', 
          borderRadius: '24px', margin: '0 auto 20px',
          display: 'flex', alignItems: 'center', justifyContent: 'center',
          boxShadow: '0 20px 40px rgba(26, 58, 95, 0.2)'
        }}>
          <ShieldCheck size={40} color="var(--accent)" />
        </div>
        <h2 style={{ fontWeight: 800, fontSize: '1.8rem', color: 'var(--primary)' }}>Scholar Portal</h2>
        <p style={{ color: 'var(--text-muted)', fontSize: '0.9rem' }}>Darul Ifta Command Center</p>
      </div>

      <div className="scholar-card" style={{ padding: '30px 20px' }}>
        <form onSubmit={handleLogin}>
          <div style={{ marginBottom: '16px' }}>
            <label style={{ display: 'block', fontWeight: 600, fontSize: '0.8rem', marginBottom: '8px', color: 'var(--text-muted)', textTransform: 'uppercase' }}>Official Email</label>
            <input 
              type="email" 
              className="scholar-input" 
              placeholder="name@darulifta.com"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
            />
          </div>
          <div style={{ marginBottom: '24px' }}>
            <label style={{ display: 'block', fontWeight: 600, fontSize: '0.8rem', marginBottom: '8px', color: 'var(--text-muted)', textTransform: 'uppercase' }}>Secure Password</label>
            <input 
              type="password" 
              className="scholar-input" 
              placeholder="••••••••"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
            />
          </div>

          {error && (
            <div style={{ background: '#fef2f2', color: '#dc2626', padding: '12px', borderRadius: '12px', fontSize: '0.85rem', marginBottom: '16px', textAlign: 'center' }}>
              {error}
            </div>
          )}

          <button type="submit" className="btn-scholar" disabled={loading}>
            {loading ? 'Authenticating...' : (
              <>
                <LogIn size={20} />
                Sign In to Portal
              </>
            )}
          </button>
        </form>
      </div>
      
      <p style={{ textAlign: 'center', fontSize: '0.8rem', color: 'var(--text-muted)', marginTop: '20px' }}>
        Protected by Darul Ifta Security System
      </p>
    </div>
  );
}
