/* ══════════════════════════════════════════════════════
   swap.js  —  Swap Modal Logic
   ══════════════════════════════════════════════════════

   DATABASE INTEGRATION POINTS (search "DB_POINT"):
   ─────────────────────────────────────────────────
   1. submitSwap()  → POST /api/swap-requests
   2. loadMySwaps() → GET  /api/swap-requests?requester_id=<userId>
   3. loadIncomingSwaps() → GET /api/swap-requests?receiver_id=<userId>
   4. respondToSwap(id, action) → PATCH /api/swap-requests/:id { status }
   ══════════════════════════════════════════════════════ */

var _swapTargetItem = null;   // the item being swapped
var _swapTargetIndex = null;

// ══════════════════════════════
// OPEN MODAL
// ══════════════════════════════
function openSwap(encodedItem, index) {
    try {
        _swapTargetItem = JSON.parse(decodeURIComponent(escape(atob(encodedItem))));
        _swapTargetIndex = index != null ? index : null;
    } catch (e) { return; }

    // Fill item preview
    document.getElementById("swapPreviewImg").src = _swapTargetItem.image || "";
    document.getElementById("swapPreviewName").textContent = _swapTargetItem.name || "";
    document.getElementById("swapPreviewCond").textContent = "Condition: " + (_swapTargetItem.condition || "—");
    document.getElementById("swapPreviewPrice").textContent = (_swapTargetItem.price || 0) + " EGP";

    // Reset form
    document.getElementById("swapOfferDesc").value = "";
    document.getElementById("swapCashTopup").value = "";
    document.getElementById("swapDiffBox").style.display = "none";
    document.getElementById("swapFormArea").style.display = "block";
    document.getElementById("swapSuccessArea").style.display = "none";

    document.getElementById("swapModal").classList.add("open");
    document.getElementById("swapOfferDesc").focus();
}

function closeSwap() {
    document.getElementById("swapModal").classList.remove("open");
}

window.openSwap = openSwap;
window.closeSwap = closeSwap;

// Close on overlay click
document.addEventListener("DOMContentLoaded", function () {
    var modal = document.getElementById("swapModal");
    if (modal) {
        modal.addEventListener("click", function (e) {
            if (e.target === modal) closeSwap();
        });
    }

    // Live price diff calc
    var topupInput = document.getElementById("swapCashTopup");
    if (topupInput) {
        topupInput.addEventListener("input", updatePriceDiff);
    }
});

// ══════════════════════════════
// PRICE DIFF
// ══════════════════════════════
function updatePriceDiff() {
    if (!_swapTargetItem) return;
    var topup = parseFloat(document.getElementById("swapCashTopup").value) || 0;
    var itemPrice = parseFloat(_swapTargetItem.price) || 0;
    var diff = itemPrice - topup;

    var box = document.getElementById("swapDiffBox");
    var diffEl = document.getElementById("swapDiffValue");
    var itemEl = document.getElementById("swapDiffItem");
    var topupEl = document.getElementById("swapDiffTopup");

    box.style.display = "flex";
    itemEl.textContent = itemPrice + " EGP";
    topupEl.textContent = topup + " EGP";
    diffEl.textContent = diff.toFixed(2) + " EGP";
    diffEl.className = "price-diff-total" + (diff > 0 ? " negative" : "");
}

// ══════════════════════════════
// SUBMIT SWAP
// ══════════════════════════════
function submitSwap() {
    if (!_swapTargetItem) return;

    var offerDesc = document.getElementById("swapOfferDesc").value.trim();
    var cashTopup = parseFloat(document.getElementById("swapCashTopup").value) || 0;

    if (!offerDesc) {
        document.getElementById("swapOfferDesc").style.borderColor = "#e74c3c";
        document.getElementById("swapOfferDesc").focus();
        return;
    }
    document.getElementById("swapOfferDesc").style.borderColor = "";

    var itemPrice = parseFloat(_swapTargetItem.price) || 0;

    // ── DB_POINT 1 ─────────────────────────────────────────────────────────
    // Shape matches SWAP_REQUESTS + SWAP_OFFER_ITEMS tables exactly.
    // Replace localStorage block with:
    //
    //   const res = await fetch("/api/swap-requests", {
    //     method : "POST",
    //     headers: { "Content-Type": "application/json" },
    //     body   : JSON.stringify(swapData)
    //   });
    //   const saved = await res.json(); // { swap_id, status, ... }
    //
    // swapData shape:
    // {
    //   swap_request: {
    //     requester_id     : <from session>,          ← replace null
    //     receiver_id      : <item owner id>,         ← replace null
    //     requested_item_id: <_swapTargetItem.id>,    ← replace index
    //     cash_topup_amount: cashTopup,
    //     status           : "pending"
    //   },
    //   swap_offer_item: {
    //     item_id          : null,                    ← id of offered item from DB
    //     offered_by_user_id: null,                   ← replace with requester id
    //     offer_description: offerDesc
    //   }
    // }
    // ───────────────────────────────────────────────────────────────────────

    var swapData = {
        swap_id: "SWP-" + Date.now(),
        created_at: new Date().toISOString(),

        // SWAP_REQUESTS fields
        swap_request: {
            requester_id: null,           // ← session user id
            receiver_id: null,           // ← item owner id
            requested_item_id: _swapTargetIndex,
            cash_topup_amount: cashTopup,
            status: "pending"
        },

        // SWAP_OFFER_ITEMS fields
        swap_offer_item: {
            item_id: null,          // ← offered item DB id
            offered_by_user_id: null,          // ← requester id
            offer_description: offerDesc
        },

        // UI-only (for display, not sent to DB)
        _ui: {
            target_item: _swapTargetItem,
            price_at_swap: itemPrice,
            price_diff: itemPrice - cashTopup,
            requester_name: "Me"                // ← replace with session user name
        }
    };

    // localStorage fallback (remove when DB connected)
    var mySwaps = JSON.parse(localStorage.getItem("mySwaps") || "[]");
    mySwaps.push(swapData);
    localStorage.setItem("mySwaps", JSON.stringify(mySwaps));
    console.log("[SwapSustain] Swap request saved:", swapData);

    // Show success
    document.getElementById("swapFormArea").style.display = "none";
    document.getElementById("swapSuccessArea").style.display = "block";
    document.getElementById("swapSuccessName").textContent = _swapTargetItem.name;
}
window.submitSwap = submitSwap;
window.updatePriceDiff = updatePriceDiff;


// ══════════════════════════════
// MY SWAPS (Buyer view)
// DB_POINT 2: replace with API call
// ══════════════════════════════
function loadMySwaps() {
    // Replace with:
    // return fetch("/api/swap-requests?requester_id=" + currentUserId).then(r => r.json());
    return JSON.parse(localStorage.getItem("mySwaps") || "[]");
}


// ══════════════════════════════
// INCOMING SWAPS (Seller view)
// DB_POINT 3: replace with API call
// ══════════════════════════════
function loadIncomingSwaps() {
    // Replace with:
    // return fetch("/api/swap-requests?receiver_id=" + currentUserId).then(r => r.json());

    // Demo: treat all swaps as "incoming" for seller demo
    return JSON.parse(localStorage.getItem("mySwaps") || "[]");
}


// ══════════════════════════════
// RESPOND TO SWAP (Seller)
// DB_POINT 4
// ══════════════════════════════
function respondToSwap(swapId, action) {
    // Replace with:
    // await fetch("/api/swap-requests/" + swapId, {
    //   method : "PATCH",
    //   headers: { "Content-Type": "application/json" },
    //   body   : JSON.stringify({ status: action })   // "accepted" | "rejected"
    // });

    var all = JSON.parse(localStorage.getItem("mySwaps") || "[]");
    all = all.map(function (s) {
        if (s.swap_id === swapId) s.swap_request.status = action;
        return s;
    });
    localStorage.setItem("mySwaps", JSON.stringify(all));
}
window.loadMySwaps = loadMySwaps;
window.loadIncomingSwaps = loadIncomingSwaps;
window.respondToSwap = respondToSwap;