import React from 'react';
import { User, Mail, Phone, LogOut, ChevronRight, Shield } from 'lucide-react';

export default function Profile() {
  const user = JSON.parse(localStorage.getItem('mufti_user') || '{}');

  const handleLogout = () => {
    localStorage.removeItem('mufti_token');
    localStorage.removeItem('mufti_user');
    window.location.href = '/';
  };

  return (
    <div className="mobile-container">
      <div style={{ padding: '30px 20px', textAlign: 'center' }}>
        <div style={{ width: '100px', height: '100px', borderRadius: '35px', background: 'var(--primary)', margin: '0 auto 16px', display: 'flex', alignItems: 'center', justifyContent: 'center', color: '#fff', fontSize: '2rem', fontWeight: 800 }}>
          {user.name?.[0]}
        </div>
        <h2 style={{ fontSize: '1.4rem', fontWeight: 800 }}>Scholar {user.name}</h2>
        <span style={{ background: 'var(--accent)', color: 'var(--primary)', padding: '4px 12px', borderRadius: '10px', fontSize: '0.75rem', fontWeight: 800, textTransform: 'uppercase' }}>Certified Mufti</span>
      </div>

      <div className="scholar-card">
        <div style={{ display: 'flex', alignItems: 'center', gap: '16px', padding: '12px 0', borderBottom: '1px solid #f1f5f9' }}>
          <Mail size={20} color="var(--text-muted)" />
          <div style={{ flex: 1 }}>
            <div style={{ fontSize: '0.75rem', color: 'var(--text-muted)' }}>Email Address</div>
            <div style={{ fontWeight: 600 }}>{user.email}</div>
          </div>
        </div>
        <div style={{ display: 'flex', alignItems: 'center', gap: '16px', padding: '12px 0' }}>
          <Phone size={20} color="var(--text-muted)" />
          <div style={{ flex: 1 }}>
            <div style={{ fontSize: '0.75rem', color: 'var(--text-muted)' }}>Mobile Signal</div>
            <div style={{ fontWeight: 600 }}>{user.mobile || 'Not provided'}</div>
          </div>
        </div>
      </div>

      <div className="scholar-card">
        <div style={{ display: 'flex', alignItems: 'center', gap: '12px', padding: '8px 0' }}>
          <Shield size={20} color="var(--primary)" />
          <div style={{ flex: 1, fontWeight: 700 }}>Security Settings</div>
          <ChevronRight size={18} color="#e2e8f0" />
        </div>
      </div>

      <div style={{ padding: '0 16px' }}>
        <button 
            onClick={handleLogout}
            style={{ background: '#fef2f2', color: '#dc2626', border: '1px solid #fee2e2', padding: '16px', borderRadius: '16px', width: '100%', display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '10px', fontWeight: 700 }}
        >
            <LogOut size={20} />
            Logout from Portal
        </button>
      </div>

      <p style={{ textAlign: 'center', fontSize: '0.7rem', color: 'var(--text-muted)', marginTop: '40px' }}>
        Scholar ID: {user.id} | App Version 1.0.0
      </p>
    </div>
  );
}
