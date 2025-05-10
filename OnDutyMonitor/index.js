const { Kafka } = require("kafkajs");
const express = require("express");
const cors = require("cors");

const app = express();
app.use(cors());
app.use(express.static("public"));

const kafka = new Kafka({
  clientId: process.env.KAFKA_CLIENT_ID || "monitor-client",
  brokers: [process.env.KAFKA_BROKER || "localhost:9092"],
});

const consumer = kafka.consumer({ groupId: "monitor-group" });
let messages = [];

const run = async () => {
  await consumer.connect();

  const admin = kafka.admin();
  await admin.connect();
  const allTopics = await admin.listTopics();
  await admin.disconnect();

  const topics = allTopics.filter(t => !t.startsWith("__"));

  console.log("🧩 Subscribing to topics:", topics);

  for (const topic of topics) {
    await consumer.subscribe({ topic, fromBeginning: true });
  }

  await consumer.run({
    eachMessage: async ({ topic, message }) => {
      try {
        const value = message.value.toString();
        const parsed = JSON.parse(value);

        messages.push({
          topic,
          timestamp: Date.now(),
          ...parsed,
        });

        if (messages.length > 200) messages.shift();

        console.log(`📩 Mensagem de [${topic}]:`, parsed);
      } catch (e) {
        console.error("❌ Erro ao processar mensagem:", e.message);
      }
    },
  });
};

app.get("/data", (req, res) => {
  res.json(messages);
});

app.listen(3000, () => {
  console.log("🟢 OnDutyMonitor rodando em http://localhost:3000");
  run().catch(console.error);
});
