export function openWorkspace(userId) {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(`asee-technician-${userId}`, 1);
        request.onupgradeneeded = () => {
            for (const name of ['meta', 'visits', 'documents', 'preferences']) request.result.createObjectStore(name);
        };
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

export function read(db, store, key) {
    return new Promise((resolve, reject) => {
        const request = db.transaction(store).objectStore(store).get(key);
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

export function all(db, store) {
    return new Promise((resolve, reject) => {
        const request = db.transaction(store).objectStore(store).getAll();
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

export function write(db, store, key, value) {
    // Resolve on commit, not on put: a request can succeed before the transaction fails.
    return new Promise((resolve, reject) => {
        const tx = db.transaction(store, 'readwrite');
        tx.objectStore(store).put(value, key);
        tx.oncomplete = resolve;
        tx.onerror = tx.onabort = () => reject(tx.error || new Error('Local save failed'));
    });
}

export function remove(db, store, key) {
    return new Promise((resolve, reject) => {
        const tx = db.transaction(store, 'readwrite');
        tx.objectStore(store).delete(key);
        tx.oncomplete = resolve;
        tx.onerror = tx.onabort = () => reject(tx.error || new Error('Local delete failed'));
    });
}

export async function syncVisit(visit, send, persist) {
    if (visit.state !== 'queued') return visit;
    // The exact signed payload remains unchanged for every retry.
    const response = await send(visit.payload);
    if (!response.ok || !response.visit_id) throw new Error('Server did not acknowledge the submission');
    const synced = {...visit, state: 'synced', receipt: response, syncError: null};
    await persist(synced);
    return synced;
}
