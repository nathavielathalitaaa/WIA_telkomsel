#!/usr/bin/env bash
set -e

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
RESULTS_DIR="$DIR/results"
mkdir -p "$RESULTS_DIR"

echo "=========================================================="
echo "TanggapTerang Automated Performance & Stress Test Suite"
echo "=========================================================="

echo "[1/4] Running Baseline Test (1 VU, 30s)..."
k6 run --vus 1 --duration 30s \
  --summary-export="$RESULTS_DIR/baseline_1vu.json" \
  "$DIR/k6_test_script.js" > "$RESULTS_DIR/baseline_1vu_summary.txt" 2>&1
echo "✓ Baseline completed."

echo "[2/4] Running Normal Load Test (5 VUs, 30s)..."
k6 run --vus 5 --duration 30s \
  --summary-export="$RESULTS_DIR/normal_5vu.json" \
  "$DIR/k6_test_script.js" > "$RESULTS_DIR/normal_5vu_summary.txt" 2>&1
echo "✓ Normal Load completed."

echo "[3/4] Running Elevated Load Test (10 VUs, 30s)..."
k6 run --vus 10 --duration 30s \
  --summary-export="$RESULTS_DIR/elevated_10vu.json" \
  "$DIR/k6_test_script.js" > "$RESULTS_DIR/elevated_10vu_summary.txt" 2>&1
echo "✓ Elevated Load completed."

echo "[4/4] Running Stress Ramp-Up Test (Up to 50 VUs, 90s)..."
k6 run \
  --summary-export="$RESULTS_DIR/stress_rampup.json" \
  "$DIR/k6_stress_rampup.js" > "$RESULTS_DIR/stress_rampup_summary.txt" 2>&1
echo "✓ Stress Ramp-Up completed."

echo "All tests finished. Summary results saved to $RESULTS_DIR."

