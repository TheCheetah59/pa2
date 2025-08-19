import test from "node:test";
import assert from "node:assert";
import { getLogoutEndpoint } from "./getLogoutEndpoint.js";

test("uses customer logout endpoint for client role", () => {
  const endpoint = getLogoutEndpoint({ role: "client" });
  assert.strictEqual(endpoint, "/api/customer/logout");
});

test("uses default logout endpoint for other roles", () => {
  const endpoint = getLogoutEndpoint({ role: "admin" });
  assert.strictEqual(endpoint, "/api/logout");
});
