import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import client from '../api/client';
import { ArrowLeft, Book, Plus, Trash2, Send, Languages } from 'lucide-react';

export default function TaskDetails() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [task, setTask] = useState(null);
  const [loading, setLoading] = useState(true);
  const [summary, setSummary] = useState('');
  const [fullAnswer, setFullAnswer] = useState('');
  const [references, setReferences] = useState([{ arabic_text: '', book_name: '', language: 'Arabic' }]);
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    fetchTask();
  }, [id]);

  const fetchTask = async () => {
    try {
      const resp = await client.get(`/mufti/questions/${id}`);
      const data = resp.data.data;
      setTask(data);
      if (data.mufti_answer) {
        setSummary(data.mufti_answer.summary || '');
        setFullAnswer(data.mufti_answer.full_answer || '');
        if (data.mufti_answer.supporting_references?.length > 0) {
          setReferences(data.mufti_answer.supporting_references);
        }
      }
    } catch (err) {} finally {
      setLoading(false);
    }
  };

  const addReference = () => {
    setReferences([...references, { arabic_text: '', book_name: '', language: 'Arabic' }]);
  };

  const removeReference = (index) => {
    setReferences(references.filter((_, i) => i !== index));
  };

  const updateReference = (index, field, value) => {
    const newRefs = [...references];
    newRefs[index][field] = value;
    setReferences(newRefs);
  };

  const handleSubmit = async () => {
    setSubmitting(true);
    try {
      await client.post(`/mufti/questions/${id}/answer`, {
        summary,
        full_answer: fullAnswer,
        references
      });
      navigate('/tasks');
    } catch (err) {
      alert('Failed to submit answer. Please try again.');
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) return <div className="mobile-container">Loading Task Details...</div>;

  return (
    <div className="mobile-container">
      {/* ── Sticky Action Header ── */}
      <div style={{ position: 'fixed', top: 0, left: 0, right: 0, background: '#fff', zIndex: 100, height: '64px', display: 'flex', alignItems: 'center', padding: '0 16px', borderBottom: '1px solid #f1f5f9' }}>
        <button onClick={() => navigate(-1)} style={{ background: 'none', border: 'none', color: 'var(--primary)' }}><ArrowLeft /></button>
        <div style={{ flex: 1, textAlign: 'center', fontWeight: 800 }}>Drafting Answer</div>
        <button 
            onClick={handleSubmit} 
            disabled={submitting}
            style={{ color: 'var(--primary)', border: 'none', background: 'none', fontWeight: 800, fontSize: '0.9rem' }}
        >
            {submitting ? '...' : <Send size={22} />}
        </button>
      </div>

      <div style={{ padding: '20px 16px' }}>
        <div className="scholar-card" style={{ margin: 0, marginBottom: '24px', background: 'var(--accent-soft)' }}>
            <div style={{ fontWeight: 800, fontSize: '0.75rem', color: 'var(--primary)', textTransform: 'uppercase', marginBottom: '8px' }}>User Inquiry</div>
            <h3 style={{ fontSize: '1.1rem', fontWeight: 800, marginBottom: '12px' }}>{task.title || 'Untitled Inquiry'}</h3>
            <div style={{ fontSize: '0.9rem', lineHeight: 1.6, color: 'var(--text-main)' }} dangerouslySetInnerHTML={{ __html: task.body }}></div>
        </div>

        {/* ── Drafting Form ── */}
        <div style={{ marginBottom: '24px' }}>
            <label className="section-label">Executive Summary (Public)</label>
            <textarea 
                className="scholar-input" 
                rows="3" 
                placeholder="Brief summary of the ruling..."
                value={summary}
                onChange={(e) => setSummary(e.target.value)}
                style={{ resize: 'none' }}
            ></textarea>
        </div>

        <div style={{ marginBottom: '24px' }}>
            <label className="section-label">Detailed Scholarly Answer</label>
            <textarea 
                className="scholar-input" 
                rows="8" 
                placeholder="Provide detailed explanation with references..."
                value={fullAnswer}
                onChange={(e) => setFullAnswer(e.target.value)}
                style={{ resize: 'none' }}
            ></textarea>
        </div>

        {/* ── Scholarly References ── */}
        <div style={{ marginBottom: '24px' }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '16px' }}>
                <label className="section-label" style={{ margin: 0 }}>Arabic Rowah & References</label>
                <button onClick={addReference} style={{ background: 'var(--primary)', color: '#fff', border: 'none', padding: '4px 12px', borderRadius: '8px', fontSize: '0.75rem', display: 'flex', alignItems: 'center', gap: '4px' }}>
                    <Plus size={14} /> Add Ref
                </button>
            </div>

            {references.map((ref, idx) => (
                <div key={idx} className="scholar-card" style={{ margin: '0 0 16px', position: 'relative' }}>
                    <button onClick={() => removeReference(idx)} style={{ position: 'absolute', right: '12px', top: '12px', background: 'none', border: 'none', color: '#ef4444' }}><Trash2 size={16} /></button>
                    
                    <div style={{ marginBottom: '12px' }}>
                        <div style={{ fontSize: '0.75rem', fontWeight: 700, marginBottom: '4px', color: 'var(--text-muted)' }}>Arabic Quotation</div>
                        <textarea 
                            className="scholar-input" 
                            rows="3" 
                            dir="rtl"
                            placeholder="...النص العربي"
                            value={ref.arabic_text}
                            onChange={(e) => updateReference(idx, 'arabic_text', e.target.value)}
                            style={{ fontSize: '1.1rem', fontFamily: 'serif' }}
                        ></textarea>
                    </div>
                    <div>
                        <div style={{ fontSize: '0.75rem', fontWeight: 700, marginBottom: '4px', color: 'var(--text-muted)' }}>Source Book</div>
                        <input 
                            type="text" 
                            className="scholar-input" 
                            placeholder="Book Name / Chapter"
                            value={ref.book_name}
                            onChange={(e) => updateReference(idx, 'book_name', e.target.value)}
                            style={{ padding: '10px 12px', height: 'auto' }}
                        />
                    </div>
                </div>
            ))}
        </div>

        <button className="btn-scholar" onClick={handleSubmit} disabled={submitting}>
            <Send size={20} />
            {submitting ? 'Submitting...' : 'Complete & Submit Review'}
        </button>
      </div>

      <style>{`
        .section-label {
            display: block;
            font-weight: 800;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 12px;
        }
      `}</style>
    </div>
  );
}
