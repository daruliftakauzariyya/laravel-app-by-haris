import React, { useState, useEffect } from 'react';
import client from '../api/client';
import { Activity, BookOpen, Clock, ChevronRight, Award } from 'lucide-react';
import { Link } from 'react-router-dom';

export default function Dashboard() {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchDashboard();
  }, []);

  const fetchDashboard = async () => {
    try {
      const resp = await client.get('/mufti/dashboard');
      setData(resp.data.data);
    } catch (err) {} finally {
      setLoading(false);
    }
  };

  if (loading) return <div className="mobile-container" style={{ textAlign: 'center', padding: '100px 0' }}>Loading Portal...</div>;

  return (
    <div className="mobile-container">
      {/* ── Scholar Identity Header ── */}
      <div style={{ padding: '20px 16px', display: 'flex', alignItems: 'center', gap: '16px' }}>
        <div style={{ width: '56px', height: '56px', borderRadius: '50%', background: 'var(--primary)', display: 'flex', alignItems: 'center', justifyContent: 'center', color: '#fff', fontSize: '1.2rem', fontWeight: 800 }}>
          {data?.user?.name?.[0]}
        </div>
        <div>
          <h2 style={{ fontSize: '1.2rem', fontWeight: 800 }}>Assalamu Alaikum</h2>
          <p style={{ color: 'var(--text-muted)', fontSize: '0.85rem' }}>Scholar {data?.user?.name}</p>
        </div>
      </div>

      {/* ── Performance Score Card ── */}
      <div className="scholar-card" style={{ background: 'var(--primary)', color: '#fff', position: 'relative', overflow: 'hidden' }}>
        <div style={{ position: 'relative', zIndex: 1 }}>
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '10px' }}>
            <span style={{ fontSize: '0.75rem', fontWeight: 700, textTransform: 'uppercase', opacity: 0.8, letterSpacing: '1px' }}>Scholar Performance Score</span>
            <Award size={18} color="var(--accent)" />
          </div>
          <div style={{ display: 'flex', alignItems: 'baseline', gap: '10px' }}>
            <h1 style={{ fontSize: '2.5rem', fontWeight: 900 }}>{data?.stats?.score}</h1>
            <span style={{ opacity: 0.6, fontSize: '0.9rem' }}>/ 100</span>
          </div>
          <div style={{ marginTop: '15px', height: '6px', background: 'rgba(255,255,255,0.1)', borderRadius: '10px' }}>
            <div style={{ width: `${data?.stats?.score}%`, height: '100%', background: 'var(--accent)', borderRadius: '10px', boxShadow: '0 0 10px var(--accent)' }}></div>
          </div>
        </div>
        <div style={{ position: 'absolute', top: '-20%', right: '-10%', width: '150px', height: '150px', background: 'radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%)', borderRadius: '50%' }}></div>
      </div>

      {/* ── High Density Stat Grid ── */}
      <div className="stat-grid">
        <div className="scholar-card" style={{ margin: 0 }}>
          <div style={{ color: '#f59e0b', marginBottom: '8px' }}><Activity size={20} /></div>
          <div style={{ fontSize: '1.2rem', fontWeight: 800 }}>{data?.stats?.assigned}</div>
          <div style={{ fontSize: '0.7rem', color: 'var(--text-muted)', fontWeight: 700, textTransform: 'uppercase' }}>Assigned</div>
        </div>
        <div className="scholar-card" style={{ margin: 0 }}>
          <div style={{ color: '#10b981', marginBottom: '8px' }}><BookOpen size={20} /></div>
          <div style={{ fontSize: '1.2rem', fontWeight: 800 }}>{data?.stats?.answered}</div>
          <div style={{ fontSize: '0.7rem', color: 'var(--text-muted)', fontWeight: 700, textTransform: 'uppercase' }}>Answers</div>
        </div>
      </div>

      {/* ── Recent Assignments Feed ── */}
      <div style={{ padding: '0 16px', display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '12px' }}>
        <h6 style={{ fontWeight: 800, textTransform: 'uppercase', fontSize: '0.75rem', letterSpacing: '1px', color: 'var(--text-muted)' }}>Urgent Assignments</h6>
        <Link to="/tasks" style={{ color: 'var(--primary)', fontSize: '0.75rem', fontWeight: 700, textDecoration: 'none' }}>View All</Link>
      </div>

      {data?.recent_tasks?.map(task => (
        <Link to={`/tasks/${task.id}`} key={task.id} style={{ textDecoration: 'none', color: 'inherit' }}>
          <div className="scholar-card" style={{ display: 'flex', gap: '12px', alignItems: 'center' }}>
            <div style={{ background: 'var(--bg)', width: '48px', height: '48px', borderRadius: '12px', display: 'flex', alignItems: 'center', justifyContent: 'center', color: 'var(--primary)' }}>
              <Clock size={20} />
            </div>
            <div style={{ flex: 1 }}>
              <div style={{ fontWeight: 700, fontSize: '0.9rem', marginBottom: '2px' }}>ID-{task.question_id}</div>
              <div style={{ color: 'var(--text-muted)', fontSize: '0.75rem', display: 'flex', gap: '8px' }}>
                <span>{task.category?.name || 'Inquiry'}</span>
                <span>•</span>
                <span>{task.madhab?.name || 'General'}</span>
              </div>
            </div>
            <ChevronRight size={18} color="#e2e8f0" />
          </div>
        </Link>
      ))}
    </div>
  );
}
