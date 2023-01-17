/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 80030
 Source Host           : localhost:3306
 Source Schema         : laravel

 Target Server Type    : MySQL
 Target Server Version : 80030
 File Encoding         : 65001

 Date: 17/01/2023 14:35:13
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for dataclient
-- ----------------------------
DROP TABLE IF EXISTS `dataclient`;
CREATE TABLE `dataclient`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `created_at` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `clientname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `yesno` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `fileinput` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `textcolumn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `checklist` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `created_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `status` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `branch` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `updated_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dataclient
-- ----------------------------
INSERT INTO `dataclient` VALUES (1, '2023-01-06 16:40:20', 'DLU', 'Jakarta Utara', 'ON PROGRESS', '1', NULL, NULL, 'dataclient-file/Zs1jv8h2tGLwraWg1AafmH1j18f505FbJy75rKxK.jpg', NULL, NULL, NULL, '1', NULL, NULL);
INSERT INTO `dataclient` VALUES (2, '2023-01-06 16:40:25', 'Prodia', 'Jakarta', 'VIP', '1', NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL);
INSERT INTO `dataclient` VALUES (12, '2023-01-10 04:39:18', 'DLU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-10 04:39:18', '1', '1', 'Margonda', '1');
INSERT INTO `dataclient` VALUES (13, '2023-01-10 09:26:59', 'DLU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-10 09:26:59', '1', '1', 'Menteng', '1');
INSERT INTO `dataclient` VALUES (14, '2023-01-10 09:27:06', 'Prodia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-10 09:27:06', '1', '1', 'Kelapa Gading', '1');

SET FOREIGN_KEY_CHECKS = 1;
