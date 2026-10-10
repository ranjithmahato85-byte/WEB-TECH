/**
 * Authentication helpers for the static Supabase frontend.
 *
 * This module only uses the publishable browser key. Privileged operations
 * remain protected by database policies and server-side functions.
 */
(function (root) {
  'use strict';

  function getClient() {
    if (!root.VotingSupabase || !root.VotingSupabase.client) {
      throw new Error('Supabase is not configured. Load supabase-client.js and the local configuration first.');
    }
    return root.VotingSupabase.client;
  }

  function requireValue(value, label) {
    if (typeof value !== 'string' || value.trim() === '') {
      throw new Error(label + ' is required.');
    }
    return value.trim();
  }

  async function signUp(details) {
    details = details || {};
    const email = requireValue(details.email, 'Email');
    const password = requireValue(details.password, 'Password');
    const fullName = requireValue(details.fullName, 'Full name');
    const citizenshipNumber = requireValue(details.citizenshipNumber, 'Citizenship number');
    const dateOfBirth = requireValue(details.dateOfBirth, 'Date of birth');
    const province = requireValue(details.province, 'Province');
    const district = requireValue(details.district, 'District');
    const municipality = requireValue(details.municipality, 'Municipality');
    const ward = requireValue(details.ward, 'Ward');

    if (password.length < 8) {
      throw new Error('Password must contain at least 8 characters.');
    }

    const response = await getClient().auth.signUp({
      email: email,
      password: password,
      options: {
        data: {
          full_name: fullName,
          citizenship_number: citizenshipNumber,
          date_of_birth: dateOfBirth,
          province: province,
          district: district,
          municipality: municipality,
          ward: ward
        }
      }
    });

    if (response.error) {
      throw response.error;
    }

    return response.data;
  }

  async function signIn(email, password) {
    email = requireValue(email, 'Email');
    password = requireValue(password, 'Password');

    const response = await getClient().auth.signInWithPassword({
      email: email,
      password: password
    });

    if (response.error) {
      throw response.error;
    }

    return response.data;
  }

  async function signOut() {
    const response = await getClient().auth.signOut();
    if (response.error) {
      throw response.error;
    }
  }

  async function getSession() {
    const response = await getClient().auth.getSession();
    if (response.error) {
      throw response.error;
    }
    return response.data.session;
  }

  async function getCurrentProfile() {
    const session = await getSession();
    if (!session) {
      return null;
    }

    const response = await getClient()
      .from('profiles')
      .select('id, full_name, role, date_of_birth, province, district, municipality, ward, verification_status, citizenship_verified, is_active, verified_at, verification_note')
      .eq('id', session.user.id)
      .maybeSingle();

    if (response.error) {
      throw response.error;
    }
    return response.data;
  }

  function onAuthStateChange(callback) {
    if (typeof callback !== 'function') {
      throw new Error('An authentication callback is required.');
    }

    const client = getClient();
    const subscription = client.auth.onAuthStateChange(function (event, session) {
      callback(event, session);
    });

    return subscription.data.subscription;
  }

  function isApprovedVoter(profile) {
    return Boolean(
      profile &&
      profile.role === 'voter' &&
      profile.is_active &&
      profile.verification_status === 'approved' &&
      profile.citizenship_verified
    );
  }

  async function reviewVoter(voterId, status, note) {
    const response = await getClient().rpc('review_voter', {
      requested_voter_id: voterId,
      requested_status: status,
      requested_note: note || null
    });
    if (response.error) {
      throw response.error;
    }
    return response.data;
  }

  async function reviewDocument(documentId, status, note) {
    const response = await getClient().rpc('review_verification_document', {
      requested_document_id: documentId,
      requested_status: status,
      requested_note: note || null
    });
    if (response.error) {
      throw response.error;
    }
    return response.data;
  }

  async function submitCitizenshipDocument(file) {
    if (!file) {
      throw new Error('Select a citizenship document first.');
    }
    const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    if (!allowedTypes.includes(file.type)) {
      throw new Error('Use a JPG, PNG, or PDF citizenship document.');
    }
    if (file.size > 5 * 1024 * 1024) {
      throw new Error('The citizenship document must be 5 MB or smaller.');
    }

    const session = await getSession();
    if (!session) {
      throw new Error('Sign in before uploading a citizenship document.');
    }

    const extension = file.name.split('.').pop().toLowerCase();
    const path = session.user.id + '/' + crypto.randomUUID() + '.' + extension;
    const storageResponse = await getClient().storage
      .from('citizenship-documents')
      .upload(path, file, { contentType: file.type, upsert: false });

    if (storageResponse.error) {
      throw storageResponse.error;
    }

    const documentResponse = await getClient()
      .from('verification_documents')
      .insert({
        voter_id: session.user.id,
        storage_path: storageResponse.data.path,
        document_type: 'citizenship',
        status: 'pending'
      })
      .select('id, status, created_at')
      .single();

    if (documentResponse.error) {
      await getClient().storage.from('citizenship-documents').remove([path]);
      throw documentResponse.error;
    }

    return documentResponse.data;
  }

  async function getMyParticipation() {
    const session = await getSession();
    if (!session) {
      throw new Error('Sign in to view your voting status.');
    }

    const response = await getClient()
      .from('voter_participation')
      .select('id, election_id, confirmation_code, voted_at')
      .eq('voter_id', session.user.id)
      .order('voted_at', { ascending: false });

    if (response.error) {
      throw response.error;
    }
    return response.data;
  }

  async function getMyDocuments() {
    const session = await getSession();
    if (!session) {
      throw new Error('Sign in to view your verification documents.');
    }

    const response = await getClient()
      .from('verification_documents')
      .select('id, document_type, status, review_note, created_at, reviewed_at')
      .eq('voter_id', session.user.id)
      .order('created_at', { ascending: false });

    if (response.error) {
      throw response.error;
    }
    return response.data;
  }

  root.VotingAuth = {
    signUp: signUp,
    signIn: signIn,
    signOut: signOut,
    getSession: getSession,
    getCurrentProfile: getCurrentProfile,
    onAuthStateChange: onAuthStateChange,
    isApprovedVoter: isApprovedVoter,
    reviewVoter: reviewVoter,
    reviewDocument: reviewDocument,
    submitCitizenshipDocument: submitCitizenshipDocument,
    getMyParticipation: getMyParticipation,
    getMyDocuments: getMyDocuments
  };
}(window));
