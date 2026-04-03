// ══════════════════════════════════════════════
// GHOSN Platform — API Placeholder Functions
// TODO: Connect to real backend API
// ══════════════════════════════════════════════

const API_BASE = '/api'; // TODO: update to real API URL

// ── Reports ───────────────────────────────────
async function submitReport(data) {
  // TODO: POST /api/reports { location, photos, description, severity }
  console.log('submitReport:', data);
  return { success: true, reportId: 'RPT_' + Date.now() };
}

async function fetchReports(filters = {}) {
  // TODO: GET /api/reports?region=...&status=...
  console.log('fetchReports:', filters);
  return {
    reports: [
      { id: 1, location: 'Riyadh', severity: 'High', status: 'Under Review', date: '2025-01-10' },
      { id: 2, location: 'Al-Qassim', severity: 'Medium', status: 'Resolved', date: '2025-01-08' },
    ]
  };
}

// ── Campaigns ─────────────────────────────────
async function fetchCampaigns(region = null) {
  // TODO: GET /api/campaigns?region=...
  console.log('fetchCampaigns:', region);
  return {
    campaigns: [
      { id: 1, title: 'Riyadh Green Belt', trees: 5000, volunteers: 120, date: '2025-02-15' },
      { id: 2, title: 'Al-Qassim Restoration', trees: 2500, volunteers: 68, date: '2025-02-22' },
    ]
  };
}

async function joinCampaign(campaignId) {
  // TODO: POST /api/campaigns/:id/join
  console.log('joinCampaign:', campaignId);
  return { success: true };
}

// ── Impact Stats ──────────────────────────────
async function fetchImpactStats() {
  // TODO: GET /api/stats/impact
  return {
    treesPlanted: 125000,
    areasRestored: 850,
    volunteers: 12400,
    co2Absorbed: 3750, // tonnes
  };
}

// ── User ──────────────────────────────────────
async function fetchUserProfile() {
  // TODO: GET /api/user/me (requires auth token)
  return null; // returns null if not logged in
}

async function fetchUserImpact(userId) {
  // TODO: GET /api/user/:id/impact
  return {
    treesPlanted: 0,
    reportsSubmitted: 0,
    campaignsJoined: 0,
    co2Saved: 0,
  };
}

// ── Search ────────────────────────────────────
async function searchReports(query) {
  // TODO: GET /api/reports/search?q=...
  console.log('search:', query);
  return { results: [] };
}
