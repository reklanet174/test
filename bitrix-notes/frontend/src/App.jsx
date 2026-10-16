import { useEffect, useState } from 'react';
import { notesApi } from './api/notesApi';

const empty = { title: '', content: '' };

export default function App() {
  const [notes, setNotes] = useState([]);
  const [id, setId] = useState(null);
  const [form, setForm] = useState(empty);
  const [errors, setErrors] = useState({});
  const [msg, setMsg] = useState('');
  const [loading, setLoading] = useState(true);

  async function load() {
    setLoading(true);
    try {
      const data = await notesApi.list();
      setNotes(Array.isArray(data) ? data : []);
    } catch (e) {
      setMsg(e.message || 'Ошибка загрузки');
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    load();
  }, []);

  function reset() {
    setId(null);
    setForm(empty);
    setErrors({});
    setMsg('');
  }

  function edit(note) {
    setId(note.id);
    setForm({ title: note.title, content: note.content });
    setErrors({});
    setMsg('');
  }

  async function onSubmit(e) {
    e.preventDefault();
    setErrors({});
    setMsg('');

    try {
      if (id) {
        await notesApi.update(id, form);
        setMsg('Сохранено');
      } else {
        await notesApi.create(form);
        setMsg('Создано');
      }
      reset();
      await load();
    } catch (err) {
      setErrors(err.errors || {});
      setMsg(err.message || 'Ошибка');
    }
  }

  async function remove(noteId) {
    if (!confirm('Удалить заметку?')) return;

    try {
      await notesApi.remove(noteId);
      if (id === noteId) reset();
      setMsg('Удалено');
      await load();
    } catch (err) {
      setMsg(err.message || 'Ошибка удаления');
    }
  }

  return (
    <div className="wrap">
      <header className="top">
        <h1>Заметки</h1>
        <a href="/docs/" target="_blank" rel="noreferrer">API docs</a>
      </header>

      {msg ? <div className="msg">{msg}</div> : null}

      <div className="cols">
        <section className="box">
          <div className="box-h">
            <strong>Список</strong>
          </div>

          {loading && <p className="muted">Загрузка...</p>}
          {!loading && !notes.length && <p className="muted">Пусто</p>}

          <ul className="list">
            {notes.map((n) => (
              <li key={n.id} className={id === n.id ? 'active' : ''}>
                <button type="button" className="item" onClick={() => edit(n)}>
                  <b>{n.title}</b>
                  <span>{n.content}</span>
                </button>
                <button type="button" className="del" onClick={() => remove(n.id)}>
                  x
                </button>
              </li>
            ))}
          </ul>
        </section>

        <section className="box">
          <div className="box-h">
            <strong>{id ? `Редактирование #${id}` : 'Новая заметка'}</strong>
          </div>

          <form onSubmit={onSubmit}>
            <label>
              Заголовок
              <input
                value={form.title}
                onChange={(e) => setForm({ ...form, title: e.target.value })}
              />
              {errors.title ? <em>{errors.title}</em> : null}
            </label>

            <label>
              Текст
              <textarea
                rows={8}
                value={form.content}
                onChange={(e) => setForm({ ...form, content: e.target.value })}
              />
              {errors.content ? <em>{errors.content}</em> : null}
            </label>

            <div className="actions">
              <button type="submit">{id ? 'Сохранить' : 'Создать'}</button>
              {id ? (
                <button type="button" onClick={reset}>
                  Отмена
                </button>
              ) : null}
            </div>
          </form>
        </section>
      </div>
    </div>
  );
}
