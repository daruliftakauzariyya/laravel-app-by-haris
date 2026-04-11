import React, { useState, useEffect } from 'react';
import client from '../api/client';
import { Search, Filter, ClipboardList, ChevronRight } from 'lucide-react';
import { Link } from 'react-router-dom';

export default function TaskList() {
  const [tasks, setTasks] = useState([]);
  const [status, setStatus] = useState('assigned');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchTasks();
  }, [status]);

  const fetchTasks = async () => {
    setLoading(true);
    try {
      const resp = await client.get(`/mufti/questions?status=${status}`);
      setTasks(resp.data.data.data);
    } catch (err) {} finally {
      setLoading(false);
    }
  };

  return (
    <div className="mobile-container">
      {/* ── Fixed Header ── */}
      <div style={{ position: 'fixed', top: 0, left: 0, right: 0, background: '#fff', zIndex: 10, padding: '10px 16px', borderBottom: '1px solid #f1f5f9' }}>
        <div style={{ display: 'flex', gap: '8px', marginBottom: '12px' }}>
          <div style={{ position: 'relative', flex: 1 }}>
            <Search size={18} style={{ position: 'absolute', left: '12px', top: '50%', transform: 'translateY(-50%)', color: 'var(--text-muted)' }} />
            <input 
              type="text" 
              className="scholar-input" 
              placeholder="Search ID or subject..." 
              style={{ paddingLeft: '40px', background: 'var(--bg)', border: 'none', height: '44px' }}
            />
          </div>
          <button style={{ background: 'var(--bg)', border: 'none', width: '44px', borderRadius: '14px', display: 'flex', alignItems: 'center', justifyContent: 'center', color: 'var(--primary)' }}>
            <Filter size={18} />
          </button>
        </div>

        {/* ── Tab Switcher ── */}
        <div style={{ display: 'flex', gap: '4px', background: 'var(--bg)', padding: '4px', borderRadius: '12px' }}>
          <button 
            onClick={() => setStatus('assigned')}
            style={{ flex: 1, border: 'none', padding: '10px', borderRadius: '10px', fontSize: '0.85rem', fontWeight: 700, background: status === 'assigned' ? '#fff' : 'transparent', color: status === 'assigned' ? 'var(--primary)' : 'var(--text-muted)', boxShadow: status === 'assigned' ? '0 2px 4px rgba(0,0,0,0.05)' : 'none' }}
          >
            Pending
          </button>
          <button 
            onClick={() => setStatus('answered')}
            style={{ flex: 1, border: 'none', padding: '10px', borderRadius: '10px', fontSize: '0.85rem', fontWeight: 700, background: status === 'answered' ? '#fff' : 'transparent', color: status === 'answered' ? 'var(--primary)' : 'var(--text-muted)', boxShadow: status === 'answered' ? '0 2px 4px rgba(0,0,0,0.05)' : 'none' }}
          >
            Answered
          </button>
        </div>
      </div>

      <div style={{ marginTop: '70px' }}>
        {loading ? (
          <div style={{ textAlign: 'center', padding: '40px 0', color: 'var(--text-muted)' }}>Updating ledger...</div>
        ) : tasks.length === 0 ? (
          <div style={{ textAlign: 'center', padding: '60px 20px', opacity: 0.5 }}>
            <ClipboardList size={48} style={{ marginBottom: '16px' }} />
            <p>No {status} inquiries found</p>
          </div>
        ) : (
          tasks.map(task => (
            <Link to={`/tasks/${task.id}`} key={task.id} style={{ textDecoration: 'none', color: 'inherit' }}>
              <div className="scholar-card" style={{ padding: '20px' }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '8px' }}>
                  <span style={{ background: 'var(--accent-soft)', color: 'var(--primary)', padding: '4px 10px', borderRadius: '8px', fontSize: '0.7rem', fontWeight: 800 }}>ID-{task.question_id}</span>
                  <span style={{ fontSize: '0.75rem', color: 'var(--text-muted)' }}>{new Date(task.created_at).toLocaleDateString()}</span>
                </div>
                <h4 style={{ fontSize: '0.95rem', fontWeight: 700, marginBottom: '10px', lineClamp: 2, display: '-webkit-box', WebkitBoxOrient: 'vertical', overflow: 'hidden' }}>
                    {task.title || stripHtml(task.body)}
                </h4>
                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginTop: '12px', paddingTop: '12px', borderTop: '1px solid #f8fafc' }}>
                    <div style={{ fontSize: '0.75rem', color: 'var(--text-muted)', display: 'flex', gap: '8px' }}>
                        <span>{task.category?.name}</span>
                        <span>•</span>
                        <span>{task.madhab?.name}</span>
                    </div>
                    <ChevronRight size={16} color="var(--primary)" />
                </div>
              </div>
            </Link>
          ))
        )}
      </div>
    </div>
  );
}

function stripHtml(html) {
    let doc = new DOMParser().parseFromString(html, 'text/html');
    return doc.body.textContent || "";
}
