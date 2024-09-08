// Initialize elliptic curve instance
const ec = new elliptic.ec('p256');

// Function to generate key pair
function generateKeyPair() {
    try {
        const keyPair = ec.genKeyPair();
        const privateKeyHex = keyPair.getPrivate('hex');
        const publicKeyHex = keyPair.getPublic('hex');
        return { status: true, sk: privateKeyHex, pk: publicKeyHex };
    } catch (error) {
        console.error('Error generating key pair:', error);
        return { status: false, message: 'Error generating key pair' };
    }
}

// Function to hash a message using SHA-256 (Web Crypto API)
async function hashMessage(message) {
    const encoder = new TextEncoder();
    const data = encoder.encode(message);
    const hashBuffer = await crypto.subtle.digest('SHA-256', data);
    return Array.from(new Uint8Array(hashBuffer)).map(b => b.toString(16).padStart(2, '0')).join('');
}

function toHex(value) {
    return value.toString('hex').padStart(64, '0');  // Ensure 64 characters
}

// Function to sign a message
async function signMessage(privateKeyHex, message) {
    try {
        const keyPair = ec.keyFromPrivate(privateKeyHex, 'hex');

        // Hash the message
        const messageHash = await hashMessage(message);

        // Sign the hashed message
        const signature = keyPair.sign(messageHash);
        const rHex = toHex(signature.r);
        const sHex = toHex(signature.s);

        // Hash the private key for linkability tag
        const linkabilityTag = await hashMessage(privateKeyHex);

        return { status: true, signature: signature, convertedSignature: { r: rHex, s: sHex }, linkabilityTag: linkabilityTag, message: 'Message signed successfully', messageHash: messageHash };
    } catch (error) {
        console.error('Error signing message:', error);
        return { status: false, message: 'Error signing message' };
    }
}


// Function to create a ring of public keys
function createRing(publicKeys) {
    try {
        const ring = publicKeys.map(pubKeyHex => ec.keyFromPublic(pubKeyHex, 'hex'));
        return { status: true, message: 'Ring created successfully', ring };
    } catch (error) {
        console.error('Error creating ring:', error);
        return { status: false, message: 'Error creating ring' };
    }
}

// Function to verify a signature
async function verifySignature(publicKeyHex, signature, message) {
    try {
        const keyPair = ec.keyFromPublic(publicKeyHex, 'hex');

        // Hash the message
        const messageHash = await hashMessage(message);

       // Create a signature object with r and s values
       const signatureObject = {
            r: signature.r,
            s: signature.s
        };

        // Verify the signature
        const verified = keyPair.verify(messageHash, signatureObject);

        return { status: true, message: 'Verification successful', verifiedSignature: verified };
    } catch (error) {
        console.error('Error verifying signature:', error);
        return { status: false, message: 'Error verifying signature' };
    }
}

